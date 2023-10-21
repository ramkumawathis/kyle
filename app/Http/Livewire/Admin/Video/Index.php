<?php

namespace App\Http\Livewire\Admin\Video;

use Illuminate\Support\Facades\Gate;
use App\Models\Video;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Index extends Component
{
   
    use WithPagination, LivewireAlert,WithFileUploads;

    protected $layout = null;

    public $search = '', $formMode = false , $updateMode = false;

    protected $videos = null;

    public  $title, $sub_title, $status = 1, $description='', $video=null,$viewMode = false;

    public $video_id =null, $video_link = null, $videoExtension = null;

    protected $listeners = [
        'show', 'edit', 'confirmedToggleAction','deleteConfirm'
    ];

    public function mount(){
        abort_if(Gate::denies('video_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
    }

    
    public function render()
    {
        return view('livewire.admin.video.index');
    }


    public function create()
    {
        $this->resetInputFields();
        $this->resetValidation();
        $this->formMode = true;
        $this->initializePlugins();
    }

    public function store()
    {
        $validatedData = $this->validate([
            'title'  => 'required',
            'sub_title'=>'required',
            'description' => 'required|without_spaces',
            'status' => 'required',
            'video' => 'required|mimes:mp4,x-flv,x-mpegURL,MP2T,3gpp,quicktime,x-msvideo,x-ms-wmv',
        ], ['without_spaces' => 'The :attribute field is required']);
        
        $validatedData['status'] = $this->status;

        $insertRecord = $this->except(['search','formMode','updateMode','video_id','video','page','paginators']);

        $video = Video::create($insertRecord);
    
        uploadImage($video, $this->video, 'video_management/video/',"video", 'original', 'save', null);

        $this->formMode = false;

        $this->resetInputFields();

        $this->flash('success',trans('messages.add_success_message'));
        
        return redirect()->route('admin.video');
       
    }


    public function edit($id)
    {
        $video = Video::findOrFail($id);

        $this->video_id = $id;
        $this->title  = $video->title;
        $this->sub_title  = $video->sub_title;
        $this->description = $video->description;
        $this->status = $video->status;
        $this->video_link = $video->video_url ?? '';
        $this->videoExtension = $video->uploadedVideo ? $video->uploadedVideo->extension : '';
        
        $this->formMode = true;
        $this->updateMode = true;

        $this->resetValidation();
        $this->initializePlugins();
    }

    public function update(){
        //dd($this->all());

        $rules = [
            'title' => 'required|string|max:'.config('constants.video_title_limit'),
            'sub_title'=>'required|string|max:'.config('constants.video_title_limit'),
            'description' => 'required|without_spaces',
            'status' => 'required',
        ];

        if($this->video){
            $extension = $this->video->getClientOriginalExtension();
            $allowedExtensions = ['mp4', 'webm', 'ogg'];
            //dd($extension);
            if (!in_array($extension, $allowedExtensions)) {
                
                $this->addError('video', 'Please use the video format mp4 only');
                return;
            }
            // $validatedData['video'] = 'required|mimes:mp4,webm,ogg|max:'.config('constants.video_max_size');
            $rules['video'] = 'required|mimes:mp4,webm,ogg';

        }

        $validatedData = $this->validate($rules, ['without_spaces' => 'The :attribute field is required']);
  
        $validatedData['status'] = $this->status;

        try{
            $video = Video::find($this->video_id);

            // Check if the photo has been changed
            $uploadId = null;
            if ($this->video) {
                if($video->uploadedVideo){
                    $uploadId = $video->uploadedVideo->id;
                    uploadImage($video, $this->video, 'video_management/video/',"video", 'original', 'update', $uploadId);
                }else{
                    uploadImage($video, $this->video, 'video_management/video/',"video", 'original', 'save', $uploadId);
                }
            }
            
            $updateRecord = $this->except(['search','formMode','updateMode','video_id','video','page','paginators']);
    
            $video->update($updateRecord);
      
            $this->formMode = false;
            $this->updateMode = false;
      
            $this->flash('success',trans('messages.edit_success_message'));
            $this->resetInputFields();
            return redirect()->route('admin.video');
        }catch (\Exception $e) {
            //  dd($e->getMessage().'->'.$e->getLine());
            
            $this->alert('success',trans('messages.error_message'));
        }
       

    }

    public function delete($id)
    {
        $this->confirm('Are you sure you want to delete it?', [
            'toast' => false,
            'position' => 'center',
            'confirmButtonText' => 'Yes, change it!',
            'cancelButtonText' => 'No, cancel!',
            'onConfirmed' => 'deleteConfirm',
            'onCancelled' => function () {
                // Do nothing or perform any desired action
            },
            'inputAttributes' => ['deleteId' => $id],
        ]);
    }

    public function deleteConfirm($id){
        $model = Video::find($id);
        
        $upload_id = $model->uploads()->first()->id;
        if($upload_id &&  deleteFile($upload_id)){
            $model->uploads()->delete();
        }
        
        $model->delete();
        
        $this->emit('refreshTable');
        
        $this->emit('refreshLivewireDatatable');

        $this->alert('success', trans('messages.delete_success_message'));
    }

    public function show($id){
        $this->video_id = $id;
        $this->formMode = false;
        $this->viewMode = true;
    }

    private function resetInputFields(){
        $this->title = '';
        $this->sub_title = '';
        $this->description = '';
        $this->status = 1;
        $this->video_link = null;
        $this->videoExtension = null;
        // $this->video_image =null;
    }

    public function cancel(){
        $this->formMode = false;
        $this->updateMode = false;
        $this->viewMode = false;
        $this->resetInputFields();
        $this->resetValidation();
    }

    public function confirmedToggleAction($data)
    {
        $id = $data['id'];
        $type = $data['type'];
        
        $model = Video::find($id);
        $model->update([$type => !$model->$type]);
        $this->alert('success', trans('messages.change_status_success_message'));
    }

    public function changeStatus($statusVal){
        $this->status = (!$statusVal) ? 1 : 0;
    }
    
    public function initializePlugins(){
        $this->dispatchBrowserEvent('loadPlugins');
    }


}

// Custom validation rule
Validator::extend('without_spaces', function ($attribute, $value, $parameters, $validator) {
    $cleanValue = trim(strip_tags($value));
    $replacedVal = trim(str_replace(['&nbsp;', '&ensp;', '&emsp;'], ['','',''], $cleanValue));
    
    if (empty($replacedVal)) {
        return false;
    }
    return true;
});