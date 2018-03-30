<?php

namespace App\Exceptions;

use App\Models\Log\LogExceptionError;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {

        $testMode = true;

        if (isset($exception)&&!$testMode) {
            $message =$exception->getMessage();
            $file =$exception->getfile();
            $line =$exception->getLine();
            $code =$exception->getCode();
            $method = $_SERVER['REQUEST_METHOD'];

            $userAgent = $_SERVER['HTTP_USER_AGENT'];

            $iPod    = stripos($userAgent, "iPod");
            $iPhone  = stripos($userAgent, "iPhone");
            $iPad    = stripos($userAgent, "iPad");
            $Android = stripos($userAgent, "Android");
          
            if ($iPod || $iPhone || $iPad) {
                $platform = "ios" ;
            } elseif ($Android) {
                $platform = "android" ;
            } else {
                $platform = "web";
            }
            $logid = LogExceptionError::setData($message, $file, $line, $code, $method, $platform);
            $lang = getLang();
            if ($lang=='en') {
                $errorMessage = 'Please contact the supporter with this error code '.$logid ;
            } else {
                $errorMessage = 'โปรดติดต่อเจ้าหน้าที่ ด้วยรหัสผิดพลาด '.$logid ;
            }



            if (strpos($request->getUri(), '/api/') !== false) {
                return response()->json(['result'=>'false','errors' => $errorMessage], 200, ['Content-type'=> 'application/json; charset=utf-8'], JSON_UNESCAPED_UNICODE);
            } else {
                return response()->view('front.error.error', ['errors'=>$errorMessage], 500);
            }
        }

      

        if ($exception instanceof TokenMismatchException) {
            // Redirect to a form. Here is an example of how I handle mine

            return redirect($request->fullUrl())->with('csrf_error', "Oops! Seems you couldn't submit form for a long time. Please try again.");
        }

        if (!env("APP_DEBUG")) {
            return response()->view('errors.500', [], 500);
        }

        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {


        if ((strpos($request->url(), 'api'))) {
            return response()->json(['result'=>false,'errors' => 'Unauthenticated.']);
        }
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest(route('login'));
    }
}
