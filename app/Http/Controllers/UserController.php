<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Validator;
use Auth;

class UserController extends Controller{

      //Get api for show user
      public function showUser($id=null){
         if($id==''){
            $users = User::get();
            return response()->json(['users'=>$users], 200);
         }else{
            $users = User::find($id);
            return response()->json(['users'=>$users], 200); 
            //200=no error...
         }  
      }

   //Post api for add user
   public function addUser(Request $request){
      if($request->ismethod('post')){
         $data = $request->all();

         // Validation
         $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
         ];

         $customMessage = [
            'name.required' => 'Name is required',
            'email.required' => 'Email is required',
            'email.email' => 'Email must be a valid email',
            'password.required' => 'Password is required',
         ];
         $validator = Validator::make($data, $rules, $customMessage);
         if ($validator->fails()) {
            return response()->json($validator->errors(),422);
         }

         $user = new User();
         $user->name = $data['name'];
         $user->email = $data['email'];
         $user->password = bcrypt($data['password']);
         $user->save();
         $message = 'User add successfully';
         return response()->json(['message'=>$message], 201); 
         //200=create...
      } 
   }

   //Post api for Add multiple user
   public function addMultipleUser(Request $request){
      if($request->ismethod('post')){
         $data = $request->all();

         // Validation
         $rules = [
            'users.*.name' => 'required',
            'users.*.email' => 'required|email|unique:users',
            'users.*.password' => 'required',
         ];

         $customMessage = [
            'users.*.name.required' => 'Name is required',
            'users.*.email.required' => 'Email is required',
            'users.*.email.email' => 'Email must be a valid email',
            'users.*.password.required' => 'Password is required',
         ];
         $validator = Validator::make($data, $rules, $customMessage);
         if ($validator->fails()) {
            return response()->json($validator->errors(),422);
         }
         $loop=0;
         foreach($data['users'] as $addUser){
            $user = new User();
            $user->name = $addUser['name'];
            $user->email = $addUser['email'];
            $user->password = bcrypt($addUser['password']);
            $user->save();
            $loop++;
            $message = $loop . ' User add successfully';
         }
         return response()->json(['message'=>$message], 201); //200=create...
      } 
   }

   //Put api for update user details
   public function updateUserDetails(Request $request, $id){
      if($request->ismethod('put')){
         $data = $request->all();

         // Validation
         $rules = [
            'name' => 'required', //email don't update anytime
            'password' => 'required',
         ];

         $customMessage = [
            'name.required' => 'Name is required',
            'password.required' => 'Password is required',
         ];
         $validator = Validator::make($data, $rules, $customMessage);
         if ($validator->fails()) {
            return response()->json($validator->errors(),422);
         }

         // $user = User::find($id);
         $user = User::findOrFail($id); 
         //Any id find or not [404 not found] message show 
         $user->name = $data['name'];
         $user->password = bcrypt($data['password']);
         $user->save();
         $message = 'User details updated successfully';
         return response()->json(['message'=>$message], 202); 
         //202=you are update data...
      } 
   }

   //Patch api for update single record
   public function updateSingleRecord(Request $request, $id){
      if($request->ismethod('patch')){ 
      //Developer will update one record using by patch...
         $data = $request->all();

         // Validation
         $rules = [
            'name' => 'required'
         ];

         $customMessage = [
            'name.required' => 'Name is required'
         ];
         $validator = Validator::make($data, $rules, $customMessage);
         if ($validator->fails()) {
            return response()->json($validator->errors(),422);
         }

         // $user = User::find($id);
         $user = User::findOrFail($id); 
         //Any id find or not [404 not found] message show 
         $user->name = $data['name'];
         $user->save();
         $message = 'User single record updated successfully';
         return response()->json(['message'=>$message], 202); 
         //202=you are update data...
      } 
   }

// Delete with paramiter(Two way)
      //Delete api for delete single user
      public function deleteSingleUser($id=null){
         $user = User::findOrFail($id)->delete(); 
         $message = 'Single user ID no['.$id. '] delete successfully';
         return response()->json(['message'=>$message], 200);
      }

      //Delete api for delete multiple user
      public function deleteMultipleUser($ids){
         $ids = explode(',', $ids);
         $user = User::whereIn('id', $ids)->delete();
         $message = 'Multiple user'.collect($ids).' delete successfully';
         return response()->json(['message'=>$message], 200);
      }

// Delete with Json(Two way)
      //Delete api for delete single user with json
      public function deleteSingleUserWithJson(Request $request){
         if($request->isMethod('delete')){
            $data = $request->all(); 
            User::where('id', $data['id'])->delete(); 
            $message = 'Single user Id no['.$data['id'].'] delete with json successfully';
            return response()->json(['message'=>$message], 200);
         }      
      }

      //Delete api for delete multiple user with json
      public function deleteMultipleUserWithJson(Request $request){
         if($request->isMethod('delete')){
            $data = $request->all(); 
            User::whereIn('id', $data['ids'])->delete();
            
            $dataList = collect($data['ids'])->pluck('id');
            $message = 'Multiple user'.$dataList.' delete with json successfully';
            return response()->json(['message'=>$message], 200);
         }      
      }

//Secure API with JWT   Link: https://jwt.io/
   //Video(11) : https://www.youtube.com/watch?v=gBeN51jXM7U&list=PLjsp2uDzfb33uFpMsT_P_t6aw87-t6t26&index=11&ab_channel=WebJourney
   public function userDeleteWithSecure(Request $request, $id){
      $header = $request->header('Authorization');
      $jwt = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkFzbGFtIiwiaWF0IjoxNTE2MjM5MDIyfQ.w02CUfQnOnoJ45iONY_fwY9BZfMMse_soOwigy8JBzA';
      
      if($header==null){
         $message = 'Authorization is required';
         return response()->json(['message'=>$message], 422);
      }else{
         if($header==$jwt){
            if ($request->isMethod('delete')) {
               $user = User::findOrFail($id)->delete(); 
               $message = 'Single user['.$id.'] delete successfully';
               return response()->json(['message'=>$message], 200);
            }
         }else{
            $message = 'Authorization does\'t match';
            return response()->json(['message'=>$message], 422);
         }
      }
   }

//Register api using passport
   //Create user table 'access_token'->nullable() column after email
   
   public function registerUserUsingPassport(Request $request){
      if($request->ismethod('post')){
         $data = $request->all();
         // return  $data;

         // Validation
         $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
         ];

         $customMessage = [
            'name.required' => 'Name is required',
            'email.required' => 'Email is required',
            'email.email' => 'Email must be a valid email',
            'password.required' => 'Password is required',
         ];
         $validator = Validator::make($data, $rules, $customMessage);
         if ($validator->fails()) {
            return response()->json($validator->errors(),422);
         }

         $user = new User();
         $user->name = $data['name'];
         $user->email = $data['email'];
         $user->password = bcrypt($data['password']);
         $user->save();

         if (Auth::attempt(['email'=>$data['email'], 'password'=>$data['password']])){
            $user = User::where('email', $data['email'])->first();
            $access_token = $user->createToken($data['email'])->accessToken;
            User::where('email', $data['email'])->update(['access_token'=>$access_token]);
            $message = 'User registerd successfully';
            return response()->json(['message'=>$message, 'access_token'=>$access_token], 201);
         }else{
            $message = 'Opps! Something went wrong';
            return response()->json(['message'=>$message], 422);
         }
      }
   }

//Login api using passport   
   public function loginUserUsingPassport(Request $request){
      if($request->ismethod('post')){
         $data = $request->all();

         // Validation
         $rules = [
            'email' => 'required|email|exists:users',
            'password' => 'required',
         ];

         $customMessage = [
            'email.required' => 'Email is required',
            'email.email' => 'Email must be a valid email',
            'email.exists' => 'Email does not exists',
            'password.required' => 'Password is required',
         ];
         $validator = Validator::make($data, $rules, $customMessage);
         if ($validator->fails()) {
            return response()->json($validator->errors(),422);
         }

         if (Auth::attempt(['email'=>$data['email'], 'password'=>$data['password']])){
            $user = User::where('email', $data['email'])->first();
            $access_token = $user->createToken($data['email'])->accessToken;
            User::where('email', $data['email'])->update(['access_token'=>$access_token]);
            $message = 'User login successfully';
            return response()->json(['message'=>$message, 'access_token'=>$access_token], 201);
         }else{
            $message = 'Invalid email or password';
            return response()->json(['message'=>$message], 422);
         }
      }
   }

}
