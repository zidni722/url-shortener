<?php
/**
 * User: zidni
 * Date: 2019-10-18
 * Time: 17:22
 */

namespace App\Http\Controllers\Linkshortener;


use App\Http\Controllers\Controller;
use App\Repositories\Linkshortener\LinkshortenerRepository;
use App\Repositories\StatusCode;
use App\Transformer\Linkshortener\LinkShortenerTransformer;
use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Support\Str;

class LinkShortenerController extends Controller
{

    protected $validation;
    protected $statusCode;
    protected $linkshortenerRepository;

    public function __construct(
        LinkshortenerRepository $linkshortenerRepository,
        ValidationFactory $validationFactory,
        StatusCode $statusCode
    )
    {
        $this->linkshortenerRepository = $linkshortenerRepository;
        $this->validation = $validationFactory;
        $this->statusCode = $statusCode;
    }

    public function index()
    {
        return response()->json("Url shortener program by zidni ilman nafi.", $this->statusCode->ok());
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $messages = self::getValidationMessages();
        $validations = self::getValidationRules();

        $validator = $this->validation->make($request->all(), $validations, $messages);
        $errorMessages = $validator->errors();

        foreach ($validations as $key => $value) {
            if ($errorMessages->has($key))
                $errors[$key] = $errorMessages->first($key);
        }

        if (!empty($errors))
            return response()->json($errors, $this->statusCode->badRequest());

        try {
            $data = [
                "code" => Str::random(6),
                "link" => $request->get("link")
            ];

            $linkShortened = $this->linkshortenerRepository->store($data);
        } catch (\Exception $e) {
            $errors = [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ];

            return response()->json($errors, $this->statusCode->inServerError());
        }

        return response((New LinkShortenerTransformer())->transformStoreSucces($linkShortened), $this->statusCode->created());
    }

    /**
     * @param Request $request
     * @param $code
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function update(Request $request, $code)
    {
        $messages = self::getValidationMessages();
        $validations = self::getValidationRules();

        $validator = $this->validation->make($request->all(), $validations, $messages);
        $errorMessages = $validator->errors();

        foreach ($validations as $key => $value) {
            if ($errorMessages->has($key))
                $errors[$key] = $errorMessages->first($key);
        }

        if (!empty($errors))
            return response()->json($errors, $this->statusCode->badRequest());

        $linkShortened = $this->linkshortenerRepository->findByColumn('code', $code);

        if (empty($linkShortened))
            return response()->json("Url not found.", $this->statusCode->notFound());

        try {
            $linkShortened->link = $request->get('link');
            $linkShortened->save();
        } catch (\Exception $e) {
            $errors = [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ];

            return response()->json($errors, $this->statusCode->inServerError());
        }

        return response((New LinkShortenerTransformer())->transformUpdateSucces($linkShortened),$this->statusCode->ok());
    }

    /**
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($code)
    {
        $linkShortened = $this->linkshortenerRepository->findByColumn('code', $code);

        if (empty($linkShortened))
            return response()->json("Url not found.", $this->statusCode->notFound());

        try {
            $linkShortened->delete();
        } catch (\Exception $e) {
            $errors = [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ];

            return response()->json($errors, $this->statusCode->inServerError());
        }

        return response()->json(['messages' => 'Url successfully deleted.'], $this->statusCode->ok());
    }

    public function redirect($code)
    {
        $linkShortened = $this->linkshortenerRepository->findByColumn('code',$code);

        if (empty($linkShortened))
            return response()->json("Url not found.", $this->statusCode->notFound());

        return redirect($linkShortened->link);
    }

    private function getValidationRules()
    {
        return [
            "link" => "required|url"
        ];
    }

    private function getValidationMessages()
    {
        return [
            'link.required' => 'Link is required field!',
            'link.url' => 'Invalid Url format!'
        ];
    }
}
