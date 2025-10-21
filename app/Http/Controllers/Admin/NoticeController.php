<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\SMException;
use App\Http\Controllers\Controller;
use App\Http\Requests\NoticeRequest;
use App\Http\Services\NoticeServices;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Throwable;

class NoticeController extends Controller
{
    private string $basePath = "notice.";
    private string $routePath = "admin.notice.";
    private string $error_message = "Oops! Something went wrong.";
    private NoticeServices $noticeServices;


    public function __construct()
    {
        $this->noticeServices = new NoticeServices();
    }

    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {
        $_notices = $this->noticeServices->getList();
        return view($this->basePath . "index", compact('_notices'));
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create()
    {
        if (checkUserRole()) {
            return view($this->basePath . "create");
        }
        if (checkUserPermission()) {
            return view($this->basePath . "create");
        }
        alert()->error('Please use  valid purchase key obtained from code canyon. Critical features are locked because the system could not verify the purchase code');
        return redirect()->route($this->routePath . "index");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param NoticeRequest $request
     * @return RedirectResponse
     */
    public function store(NoticeRequest $request)
    {
        if (checkUserRole()) {
            try {
                $this->noticeServices->saveNotice($request);
                alert()->success('Success', 'Notice been created successfully');
            } catch (Throwable $e) {
                alert()->error($this->error_message, $e->getMessage());
            }
            return redirect()->route($this->routePath . "index");
        }
        if (checkUserPermission()) {
            try {
                $this->noticeServices->saveNotice($request);
                alert()->success('Success', 'Notice been created successfully');
            } catch (Throwable $e) {
                alert()->error($this->error_message, $e->getMessage());
            }
            return redirect()->route($this->routePath . "index");
        }
        alert()->error('Please use  valid purchase key obtained from code canyon. Critical features are locked because the system could not verify the purchase code');
        return redirect()->route($this->routePath . "index");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Application|Factory|View|\Illuminate\Foundation\Application|RedirectResponse
     */
    public function edit(int $id)
    {
        if (checkUserRole()) {
            try {
                $_notice = $this->noticeServices->getNotice($id);
                return view($this->basePath . "edit", compact('_notice'));
            } catch (Throwable $e) {
                alert()->error($this->error_message, $e->getMessage());
            }
        }
        if (checkUserPermission()) {
            try {
                $_notice = $this->noticeServices->getNotice($id);
                return view($this->basePath . "edit", compact('_notice'));
            } catch (Throwable $e) {
                alert()->error($this->error_message, $e->getMessage());
            }
        }
        alert()->error('Please use  valid purchase key obtained from code canyon. Critical features are locked because the system could not verify the purchase code');
        return redirect()->route($this->routePath . "index");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param NoticeRequest $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(NoticeRequest $request, int $id): RedirectResponse
    {
        try {
            $this->noticeServices->updateNotice($id, $request);
            alert()->success('Success', 'Notice been updated successfully');
        } catch (Throwable $e) {
            alert()->error($this->error_message, $e->getMessage());
        }
        return redirect()->route($this->routePath . "index");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->noticeServices->deleteNotice($id);
            alert()->success('Success', 'Notice has been deleted');
        } catch (Throwable $e) {
            alert()->error($this->error_message, $e->getMessage());
        }
        return redirect()->route($this->routePath . "index");
    }

    public function changeStatus(int $id): array
    {
        try {
            return $this->noticeServices->changeStatus($id);
        } catch (Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
