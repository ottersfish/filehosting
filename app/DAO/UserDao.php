<?php
/**
* 
*/
class UserDao extends User
{
    protected $logDao;

    public function checkAvailbility($username, $email){
        $errors = array();
        $users = $this->getUserByUsername($username);
        $emails = $this->getUserByEmail($email);
        if(!is_null($users))array_push($errors, 'Username has already taken');
        if(!is_null($emails))array_push($errors, 'Email has already been registered');
        return $errors;
    }

    public function deleteUserById($id){
        $query = $this->where('id', $id);
        $rowDeleted = $query->get()->first();
        LogDao::logDelete($this->table, $rowDeleted->email.', '.$rowDeleted->username);
        $query->delete();
        return $rowDeleted->username;
    }

    public function editProfile($id){
        $query = $this->where('id', $id);
        $row = $query->get(array('name', 'password'))->first();
        $query->update(array(
            'name' => Input::get('name'),
            'password' => Hash::make(Input::get('password'))
            ));
        LogDao::logEdit($this->table, 'users', 
            $row->name.', '.$row->password, 
            Input::get('name').', '.Hash::make(Input::get('password')))    ;
        return true;
    }

    public function getProfile($id){
        return $this->where('id', $id)->get(array('name'))->first();
    }

    public function getUsers(){
        return $this->all();
    }

    public function getUserById($id){
        return $this->find($id);
    }

    public function getUserByEmail($email_address){
        return $this->where('email', $email_address)->pluck('id');
    }

    public function getUserByUsername($username){
        return $this->where('username', $username)->pluck('id');
    }

    public function getUserByUsernameEmail($username, $email){
        return $this->where('username', $username)->where('email', $email)->get(array('id'))->first();
    }

    public function saveUser($regData){
        LogDao::logCreate($this->table, 'email, username, password, name', $regData['email'].', '.$regData['username']);
        $regData['password'] = Hash::make($regData['password']);
        return $this->create($regData);
    }

    public function userExists($id){
        return $this->find($id);
    }
    
    public function validateEditProf($updData){
        $rules=array(
            'name' => 'required',
            'password' => 'required',
            'con_password' => 'required|same:password',
        );

        return Validator::make($updData, $rules);
    }

    public function validateLogin($credentials){
        $messages = array(
            'alphanum' => 'Only characters and number are allowed for :attribute.',
        );

        $rules=array(
            'username' => 'required|alphanum',
            'password' => 'required'
        );
        return Validator::make($credentials, $rules, $messages);
    }

    public function validateRegister($regData){
        $messages=array(
            'alphanum' => 'Only characters and number are allowed for :attribute.'
        );

        $rules=array(
            'email' => 'required|email',
            'username' => 'required|alphanum',
            'password' => 'required',
            'con_password' => 'required|same:password',
            'name' => 'required'
        );

        return Validator::make($regData, $rules, $messages);
    }
}
?>