<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ContactController extends Controller
{
   public function index()
    {
        return view('client.contact'); // resources/views/client/contact.blade.php
    }
}
