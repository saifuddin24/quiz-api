<?php

namespace App\Http\Controllers;

use App\Category;
use App\CatRelation;
use App\Http\Resources\CategoryResource;
use Doctrine\DBAL\Query\QueryBuilder;
use Illuminate\Database\Schema\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Version\Extension\Build;

class NotFoundController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( ){
        return response( ['message' => 'Requested api not found' ], 404);
    }

}
