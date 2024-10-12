<?php

namespace App\Http\Controllers;

use App\Models\User;
use Aws\S3\S3Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{

    private $s3Client;

    public function __construct()
    {

        $this->s3Client = new S3Client([
            'version' => 'latest',
            'region'  => env('AWS_DEFAULT_REGION'),
            'credentials' => [
                'key'    => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
            'http' => [
                'verify' => false,  
            ],
        ]);
        
    }

    public function register(Request $request)
    {

        $data = $request->validate([
            'fullname' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|required|confirmed',
            'password_confirmation' => 'required',
        ]);
    
        User::create($data);
    
        return redirect()->route('login.page');   
        
    }

    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string'
            ]);
    
            $isAuthenticated = Auth::attempt($credentials);
    
            if($isAuthenticated){
                $request->session()->regenerate();
                return redirect()->route('project_files.page');
            }
    
        } catch (\Exception $e) {
            Log::error(
                'Error registering logging in user: '. $e->getMessage(), 
                ['trace' => $e->getTraceAsString()]
            );

            return back()->withErrors([
                'email' => 'The provided credentials do not match our records'
            ])->onlyInput('email');
        }
        
        
    }

    public function editProfile(Request $request)
    {
        try {

            $credentials = $request->validate([
                'user_id' => 'required|numeric',
                'fullName' => 'required|string',
                'email' => 'required|string|email',
                'oldPassword' => 'required|string',
                'newPassword' => 'required|string|min:8',
            ]);

            Log::debug("Credentials Edit: ", [
                'credentials' => $credentials
            ]);

            $user = User::select('password')->where('id', $credentials['user_id'])->first();

            if($user && Hash::check($credentials['oldPassword'], $user->password)) {
                $updatedUser = User::where('id', $credentials['user_id'])->update([
                    'fullname' => $credentials['fullName'],
                    'email' => $credentials['email'],
                    'password' => Hash::make($credentials['newPassword']),
                ]);

                Log::log(1, "Updated User" . $updatedUser);

                return back()->with('success', 'Profile Updated Successfully.');

            } else {
                return back()->withErrors([
                    'oldPassword' => 'Inputted Old Password do not match in our Records'
                ])->onlyInput('oldPassword');
            }

            return back()->with('error', 'Error In Editing Profile!');

        } catch (\Illuminate\Validation\ValidationException $e) {
           return back()->withErrors($e->errors())->withInput();

        } catch (\Throwable $th) {

            Log::error("Error in Editing Profile: " . $th->getMessage());
            return back()->with('error', 'Something went wrong!');
        }
        
    }


    public function uploadProfilePicture(Request $request)
    {
        try {
            
            $request->validate([
                'file' => 'required|file|mimes:jpeg,jpg,png|max:2048',
            ],
            [
                'file.mimes' => 'The uploaded file must be a JPG, JPEG, PNG.',
                'file.max' => 'The uploaded file must not be larger than 2MB.',
            ]);
    
            if($request->hasFile('file')){
                $file = $request->file('file');
                $fileName = $file->getClientOriginalName();
    
                $filePath = 'picture/' . Auth::id() . '/' . $fileName;
    
                $result = $this->s3Client->putObject([
                    'Bucket' => env('AWS_BUCKET'),
                    'Key'    => $filePath,
                    'SourceFile' => $file->getRealPath(),
                    'ACL'    => 'private', 
                ]);
    
                $path = $result['ObjectURL'];
    
                Log::debug('UPLOAD RESULT: ', [
                    'path' => $path,
                    'date' => date('M d, Y h:i A', time())
                ]);
    
                return back()->with('success', 'File uploaded successfully.');
            }

        } catch (\Exception $e) {
            Log::error(
                'Error uploading picture in S3 bucket: ' . $e->getMessage(), 
            );

            return back()->withErrors([
                'file' => 'There was a problem uploading your picture, please try again'
            ])->onlyInput('file');
        }

        
    }


    public function logout(Request $request)
    {
        Auth::logout();
 
        $request->session()->invalidate();
    
        $request->session()->regenerateToken();
    
        return redirect()->route('login.page');
    }
}
