<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class NoticeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('Notice/Index', [
            'filters' => Request::all('search', 'trashed'),
            'notices' => Auth::user()->account->notices()
                ->orderBy('created_at', 'DESC')
                ->filter(Request::only('search', 'trashed'))
                ->paginate()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Notice $notice)
    {

        Request::validate([
            'notice_type' => ['required', 'max:255', 'min:2'],
            'notice_subject' => ['required', 'max:255', 'min:2'],
            'notice_description' => ['required', 'min:12', 'max:255'],
            'file' => ['required', 'mimes:pdf,docx,doc', 'max:10240'],
        ]);

        $notice->create([
            'notice_type' => Request::input('notice_type'),
            'notice_subject' => Request::input('notice_subject'),
            'notice_description' => Request::input('notice_description'),
            'file_name' => Request::input('file_name'),
            'file' => Request::file('file') ? Request::file('file')->store('notice', 'public') : null,
        ]);

        return Redirect::back()->with('success', 'Notice added.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        Request::validate([
            'notice_type' => ['required', 'max:255', 'min:2'],
            'notice_subject' => ['required', 'max:255', 'min:2'],
            'notice_description' => ['required', 'min:12', 'max:255'],
            'file' => ['required', 'mimes:pdf,docx,doc', 'max:10240'],
        ]);

        Notice::where('id', $id)->update([
            'notice_type' => Request::input('notice_type'),
            'notice_subject' => Request::input('notice_subject'),
            'notice_description' => Request::input('notice_description'),
            'file_name' => Request::input('file_name'),
            'file' => Request::file('file') ? Request::file('file')->store('notice', 'public') : null,
        ]);

        return Redirect::back()->with('success', 'Notice updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Notice::find($id)->delete();

        return Redirect::back()->with('success', 'Notice deleted.');
    }

    public function restore($id)
    {
        Notice::where('id', $id)->restore([
            'deleted_at' => null,
        ]);

        return Redirect::back()->with('success', 'Notice restored.');
    }
}
