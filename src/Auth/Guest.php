<?php
namespace kennydude\Wiki\Auth;

class Guest extends User {
    public function __construct(){}

    public function inGroup($group){
        if($group == "guests"){ return true; }
        return false;
    }

    public function is_guest(){
        return true;
    }
}
