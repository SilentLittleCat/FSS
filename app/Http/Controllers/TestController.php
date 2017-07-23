<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Auth;
use Adldap;
use App\User;
use App\Project;
use App\Checklist;
use DB;
use Storage;
use Carbon\Carbon;
use App\File;
use Illuminate\Support\Str;
use Zipper;

class TestController extends Controller
{
    public function index()
    {
        return 'no';
    }
}
