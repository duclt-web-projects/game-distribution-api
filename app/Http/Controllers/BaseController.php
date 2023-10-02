<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Traits\FilterBuilderTrait;
use Illuminate\Support\Facades\Validator;

class BaseController extends Controller
{
    use FilterBuilderTrait;
    protected $service;
    protected $limit = 0;
    protected $perPage = 10;

    /**
     * @param Request $request
     * @param array $rules
     * @return array
     */
    public function validate(Request $request, array $rules = []): array
    {
        if (count($rules) === 0) return [];

        $validator = Validator::make($request->all(), $rules);

        $errors = [];
        if ($validator->fails()) {
            foreach ($validator->errors()->messages() as $fieldErrors) {
                foreach ($fieldErrors as $error) {
                    $errors[] = $error;
                }
            }
        }
        return $errors;
    }

    public function handleError(array $errors, int $status = 400): JsonResponse
    {
        foreach ($errors as $error) {
            return response()->json(['message' => $error], $status);
        }

        return response()->json(['message' => 'No Content'], 204);
    }
}
