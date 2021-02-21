<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\Note as NoteResource;
use App\Models\Note;
use Illuminate\Contract\
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NoteController extends BaseController
{
    public function index()
    {
        $note = Note::all();

        return $this->sendResponse(NoteResource::collection($note), 'Note recieved successfully');
    }

    public function userNotes($id)
    {
        $note = Note::where('user_id', $id)->get();

        return $this->sendResponse(NoteResource::collection($note), 'Note recieved successfully');
    }


    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'title' => 'required',
            'content' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Please validate error', $validator->errors());
        }

        $user = Auth::user();
        $input['user_id'] = $user->id;
        $note = Note::create($input);
        return $this->sendResponse( $note, 'Note created successfully');
    }

    public function show($id)
    {
        $note = Note::find($id);
        if (is_null($note)) {
            return $this->sendError('Note not found');
        }

        return $this->sendResponse(new NoteResource($note), 'Note found successfully');
    }

    public function update(Request $request, Note $note)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'title' => 'required',
            'content' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Please validate error', $validator->errors());
        }

        $note->title = $input['title'];
        $note->content = $input['content'];
        $note->save();

        return $this->sendResponse(new NoteResource($note), 'Note updated successfully');
    }

    public function destroy(Note $note)
    {
        $note->delete();

        return $this->sendResponse(new NoteResource($note), 'Note deleted successfully');
    }
}
