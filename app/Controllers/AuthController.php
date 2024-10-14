<?php

namespace App\Controllers;

use App\Entities\Cast\CastUuid;
use App\Entities\UserEntity;
use App\Models\OtpRequestModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use Codewrite\CoopAuth\CoopResponse;
use Codewrite\CoopAuth\GuardReponse;
use Codewrite\CoopAuth\Sms;
use Config\Services;
use Exception;
use Firebase\JWT\ExpiredException;

class AuthController extends ResourceController
{
    protected $format    = 'json';
    private $emailService;
    private $smsService;

    public function __construct()
    {
        // Initialize Email and SMS Service
        $this->emailService = Services::email();
        $this->smsService = new Sms();  // Inject the SMS service class here
    }

    /**
     * Return an array of resource objects, themselves in array format.
     *
     * @return ResponseInterface
     */
    public function authorize()
    {
        $rules = [
            'username' => 'permit_empty|alpha_numeric',
            'email' => 'permit_empty|valid_email',
            'phone' => 'permit_empty|numeric',
            'password' => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->respond([
                'status' => false,
                'data' => null,
                'message' => "Invalid credentials",
                'errors' => $this->validator->getErrors()
            ], Response::HTTP_BAD_REQUEST);
        }

        $identifiers = $this->validator->getValidated();
        unset($identifiers['password']);
        $password = $this->request->getVar('password');

        $userModel = new UserModel();
        $user = $userModel->where($identifiers)->first();

        if (!$user)
            return (new GuardReponse(false, GuardReponse::INVALID_CREDENTIALS))->responsed();

        // if using email to login check if its verified
        if (isset($identifiers['email']) && !$user->email_verified)
            return (new GuardReponse(false, GuardReponse::UNVERIFIED_EMAIL))->responsed();

        // if using phone to login check if its verified
        if (isset($identifiers['phone']) && !$user->phone_verified)
            return (new GuardReponse(false, GuardReponse::UNVERIFIED_PHONE))->responsed();

        // Replace with your user authentication logic
        if ($user->verifyPassword($password)) {
            $accessPayload = auth()->generatePermissions($user->id);
            // Refresh Token Payload (valid for 7 days)
            $refreshPayload = [
                'sub' => $user->id,
            ];

            $accessToken = auth()->generateAccessToken($accessPayload);
            $refreshToken = auth()->generateRefreshToken($refreshPayload);

            return $this->respond([
                'status' => true,
                'data' => $user,
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken
            ], Response::HTTP_OK);
        }

        return $this->respond([
            'status' => false,
            'data' => null,
            'message' => "Invalid credentials"
        ], Response::HTTP_OK);
    }

    /**
     * Return an array of resource objects, themselves in array format.
     *
     * @return ResponseInterface
     */
    public function refresh()
    {
        try {
            $refreshToken = $this->request->getVar('refresh_token');
            $claims = auth()->decodeToken($refreshToken);
            // get new token
            $accessPayload = auth()->generatePermissions($claims->sub ?? null);
            $newAccessToken = auth()->generateAccessToken($accessPayload);

            // Refresh Token Payload
            $refreshPayload = [
                'sub' => $claims->sub ?? null,
            ];
            $refreshToken = auth()->generateRefreshToken($refreshPayload);
            $userModel = new UserModel();

            return $this->respond([
                'status' => true,
                'data' => $userModel->find($claims->sub),
                'access_token' => $newAccessToken,
                'refresh_token' => $refreshToken
            ]);
        } catch (ExpiredException $e) {
            return new GuardReponse(false, CoopResponse::TOKEN_EXPIRED);
        } catch (Exception $e) {
            return new GuardReponse(false, CoopResponse::INVALID_TOKEN);
        }
    }

    // Request OTP or Verification Link for Email or Phone
    public function requestOtp()
    {
        $userModel = new UserModel();
        $otpRequestModel = new OtpRequestModel();

        $rules = [
            'identifier' => 'required|string',
            'type'       => 'string|in_list[email_verification,phone_verification,password_reset]',
            'callbackUrl' => 'required|string'
        ];

        // Validate input
        if (!$this->validate($rules)) {
            return $this->response->setJSON(
                [
                    'status'  => false,
                    'message' => 'Failed validating data',
                    'error'   => $this->validator->getErrors()
                ]
            )->setStatusCode(Response::HTTP_BAD_REQUEST);
        }


        $identifier = $this->request->getVar('identifier');  // Can be email or phone
        $callbackUrl = $this->request->getVar('callbackUrl');  // Can be email or phone
        $type = $this->request->getVar('type');              // Type: email_verification, phone_verification, or password_reset

        // Find the user by email or phone
        $user = filter_var($identifier, FILTER_VALIDATE_EMAIL)
            ? $userModel->where('email', $identifier)->first()
            : $userModel->where('phone', $identifier)->first();

        if (!$user)
            return $this->response->setJSON([
                'status'  => false,
                'message' => 'User not found',
                'error'   => []
            ])->setStatusCode(Response::HTTP_NOT_FOUND);


        // Generate token and OTP
        $token = bin2hex(random_bytes(16));
        $otp = random_int(100000, 999999);  // 6-digit OTP for phone verification and password reset
        $expiresAt = date('Y-m-d H:i:s', strtotime('+15 minutes'));

        // Save OTP request to the database
        $otpRequestModel->save([
            'user_id' => $user->id,
            'type' => $type,
            'identifier' => $identifier,
            'otp' => $otp,
            'token' => $token,
            'expires_at' => $expiresAt,
        ]);

        // Send based on request type
        if ($type == 'password_reset') {
            // check if user has permission to change password
            $condition =['change_password' => $user->id];

            $response = auth()->canUser($user->id, 'update', 'users', $condition);
            if ($response->denied())
                return $response->responsed(null, "This account's password cannot be changed.");

            // Send Password Reset Link or OTP
            if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
                // Send Password Reset Email
                $this->emailService->setTo($identifier);
                $this->emailService->setSubject('Password Reset Request');
                $this->emailService->setMessage("Your password reset link:$callbackUrl?token=$token\nOTP: " . $otp);
                $this->emailService->send();

                if ($this->emailService->send())
                    $this->respond([
                        'status' => false,
                        'message' => "Password Reset Request sent successfully.",
                    ], Response::HTTP_OK);
            } else {
                $tmp = 'Your OTP for password reset is: {$otp}';
                // Send Password Reset SMS
                $response = $this->smsService->send($tmp, [['phone' => $identifier, 'otp' => $otp]]);

                if (!$response->getStatus())
                    $this->respond([
                        'status' => false,
                        'message' => $response->getMessage(),
                    ], Response::HTTP_OK);
            }
        } elseif ($type == 'email_verification') {
            // Send Email Verification Link
            $this->emailService->setTo($identifier);
            $this->emailService->setSubject('Email Verification');
            $this->emailService->setMessage("Your email verification link: " . site_url('user/verify-email/' . $token) . "\nOTP: " . $otp);
            if ($this->emailService->send())
                $this->respond([
                    'status' => false,
                    'message' => "OTP sent successfully.",
                ], Response::HTTP_OK);
        } elseif ($type == 'phone_verification') {
            // Send Phone Verification OTP
            $tmp = 'Your OTP for phone verification is: {$otp}';
            $response = $this->smsService->send($tmp, [['phone' => $identifier, 'otp' => $otp]]);

            if (!$response->getStatus())
                $this->respond([
                    'status' => false,
                    'message' => $response->getMessage(),
                ], Response::HTTP_OK);
        }

        return $this->respond([
            'status' => true,
            'message' => 'OTP sent successfully.'
        ], Response::HTTP_OK);
    }

    // Verify OTP or Token for Email or Phone
    public function verifyOtpOrToken()
    {
        $otpRequestModel = new OtpRequestModel();
        $userModel = new UserModel();

        $rules = [
            'identifier' => 'required|string',
            'type'       => 'string|in_list[email_verification,phone_verification,password_reset]',
        ];

        // Validate input
        if (!$this->validate($rules)) {
            return $this->response->setJSON(
                [
                    'status'  => false,
                    'message' => 'Failed validating data',
                    'error'   => $this->validator->getErrors()
                ]
            )->setStatusCode(Response::HTTP_BAD_REQUEST);
        }

        $identifier = $this->request->getVar('identifier');  // Email or Phone
        $otp = $this->request->getVar('otp');                // For phone or password reset
        $token = $this->request->getVar('token');            // For email verification or password reset
        $type = $this->request->getVar('type');              // Type: email_verification, phone_verification, or password_reset

        // Find the OTP request
        $otpRequest = $otpRequestModel
            ->where('type', $type)
            ->groupStart()
            ->where('identifier', $identifier)
            ->groupEnd()
            ->orderBy("id", 'desc')
            ->first();

        if (!$otpRequest || strtotime($otpRequest->expires_at) < time()) {
            return $this->response->setJSON(
                [
                    'status'  => false,
                    'message' => $type === 'phone_verification' ? 'Invalid or expired OTP' : 'Invalid or expired Token',
                    'error'   => null
                ]
            )->setStatusCode(Response::HTTP_BAD_REQUEST);
        }

        // Verify based on the provided token or OTP
        if ($otp && $otpRequest->otp !== $otp) {
            return $this->response->setJSON(
                [
                    'status'  => false,
                    'message' => 'Invalid OTP',
                    'error'   => null
                ]
            )->setStatusCode(Response::HTTP_UNAUTHORIZED);
        }

        if ($token && $otpRequest->token !== $token) {
            return $this->response->setJSON(
                [
                    'status'  => false,
                    'message' => 'Invalid token',
                    'error'   => null
                ]
            )->setStatusCode(Response::HTTP_UNAUTHORIZED);
        }

        // Update user based on the verification type
        if ($type == 'email_verification') {
            $userModel->update($otpRequest->user_id, ['email_verified' => 1]);
        } elseif ($type == 'phone_verification') {
            $userModel->update($otpRequest->user_id, ['phone_verified' => 1]);
        }

        // Delete old OTP request
        $otpRequestModel->where('type', $type)->where('identifier', $identifier)->delete();

        return $this->respond([
            'status' => true,
            'message' => 'Verification successful.'
        ]);
    }

    // Reset Password with OTP or Token
    public function resetPassword($token = null)
    {
        if (!$token)
            return $this->respond([
                'status'  => false,
                'message' => 'Provide token in the route path reset-password/{token}.',
                'error'   => null
            ], Response::HTTP_BAD_REQUEST);

        $rules = [
            'new_password' => 'required|min_length[6]',
        ];

        // Validate input
        if (!$this->validate($rules)) {
            return $this->respond([
                'status'  => false,
                'message' => 'Failed validating data',
                'error'   => $this->validator->getErrors()
            ], Response::HTTP_BAD_REQUEST);
        }

        $otpRequestModel = new OtpRequestModel();
        $userModel = new UserModel();

        // Find the OTP request by token
        $otpRequest = $otpRequestModel->where('token', $token)->first();

        if (!$otpRequest || strtotime($otpRequest->expires_at) < time()) {
            return $this->respond([
                'status'  => false,
                'message' => 'Invalid or expired token',
                'error'   => $this->validator->getErrors()
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = $userModel->find($otpRequest->user_id);
        $user->setPassword($this->request->getVar('new_password'));
        $userModel->save($user);

        // Delete OTP request
        $otpRequestModel->delete($otpRequest->id);

        return $this->respond([
            'status' => true,
            'message' => 'Password reset successful.'
        ]);
    }

    // 3. Reset Password with OTP or Token
    public function resetPassword2()
    {
        $rules = [
            'otp'           => 'required|max_length[6]|',
            'identifier'    => 'required|string',
            'new_password'  => 'required|min_length[6]',
        ];

        // Validate input
        if (!$this->validate($rules)) {
            return $this->respond([
                'status'  => false,
                'message' => 'Failed validating data',
                'error'   => $this->validator->getErrors()
            ], Response::HTTP_BAD_REQUEST);
        }

        $otpRequestModel = new OtpRequestModel();
        $userModel = new UserModel();

        // Find the OTP request by token
        $otpRequest = $otpRequestModel->where('otp', $this->request->getVar('otp'))
            ->where('identifier', $this->request->getVar('identifier'))
            ->where('type', 'password_reset')
            ->first();

        if (!$otpRequest || strtotime($otpRequest->expires_at) < time()) {
            return $this->respond([
                'status'  => false,
                'message' => 'Invalid or expired otp',
                'error'   => $this->validator->getErrors()
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = $userModel->find($otpRequest->user_id);
        $user->setPassword($this->request->getVar('new_password'));
        $userModel->save($user);

        // Delete OTP request
        $otpRequestModel->delete($otpRequest->id);

        return $this->respond([
            'status' => true,
            'message' => 'Password reset successful.'
        ]);
    }
}
