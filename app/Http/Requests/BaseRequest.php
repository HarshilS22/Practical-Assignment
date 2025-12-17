<?php

    namespace App\Http\Requests;

    use Illuminate\Contracts\Validation\Validator;
    use Illuminate\Foundation\Http\FormRequest;
    use Illuminate\Validation\UnauthorizedException;
    use Illuminate\Validation\ValidationException;

    class BaseRequest extends FormRequest
    {
        /**
         * To throw Exception when the request in Unauthorised
         *
         * @return mixed
         */
        public function failedAuthorization(): mixed
        {
            throw new UnauthorizedException('You are forbidden from accessing that resource.', 403);
        }

        /**
         * Customised validation error, additionally create logs
         *
         * @param  \Illuminate\Contracts\Validation\Validator  $validator
         * @return mixed
         * @throws \Illuminate\Validation\ValidationException
         */
        public function failedValidation(Validator $validator): mixed
        {
            //$response = response()->json(['success' => false, 'data' => [], 'error_message' => $errorMessage], 422); //@todo: Looks like this redundant, will be removed in a month

            throw ValidationException::withMessages(['error_message' => $validator->errors()->first()]);
        }
    }
