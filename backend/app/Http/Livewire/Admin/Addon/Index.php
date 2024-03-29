<?php

namespace App\Http\Livewire\Admin\Addon;

use Illuminate\Support\Facades\Gate;
use App\Models\Addon;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Symfony\Component\HttpFoundation\Response;


class Index extends Component
{
   
    use WithPagination, LivewireAlert,WithFileUploads;

    protected $layout = null;

    public $search = '', $formMode = false , $updateMode = false;

    protected $addons = null;

    public  $title, $price, $credit, $status = 1, $viewMode = false;

    public $addon_id =null;

    protected $listeners = [
        'confirmedToggleAction','deleteConfirm'
    ];

    public function mount(){
        abort_if(Gate::denies('addon_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
    }

    public function render()
    {
        $this->search = str_replace(',', '', $this->search);
         $this->addons = Addon::query()
            ->where('title', 'like', '%'.$this->search.'%')
            ->OrWhere('price', 'like', '%'.$this->search.'%')
            ->OrWhere('credit', 'like', '%'.$this->search.'%')
            ->orderBy('id','desc')
            ->paginate(10);

        $allAddons = $this->addons;
        return view('livewire.admin.addon.index',compact('allAddons'));
    }

    public function create()
    {
        $this->resetInputFields();
        $this->formMode = true;
        $this->initializePlugins();
    }

    public function store()
    {
        $validatedData = $this->validate([
            'title'  => 'required',
            'price' => 'required|numeric|min:0|max:99999999.99',
            'credit' => 'required|numeric',
            'status' => 'required',
        ]);
        
        $validatedData['status'] = $this->status;

        $insertRecord = $this->except(['search','formMode','updateMode','addon_id','image','originalImage','page','paginators']);

        $addon = Addon::create($insertRecord);
    
        $this->formMode = false;

        $this->resetInputFields();

        $this->flash('success',trans('messages.add_success_message'));
        
        return redirect()->route('admin.addon');
       
    }


    public function edit($id)
    {
        $addon = Addon::findOrFail($id);

        $this->addon_id = $id;
        $this->title  = $addon->title;
        $this->price = $addon->price;
        $this->credit = $addon->credit;
        $this->status = $addon->status;

        $this->formMode = true;
        $this->updateMode = true;
        $this->initializePlugins();
    }

    public function update(){
        $validatedData = $this->validate([
            'title' => 'required',
            'price' => 'required|numeric|min:0|max:99999999.99',
            'credit' => 'required|numeric',
            'status' => 'required',
        ]);
  
        $validatedData['status'] = $this->status;

        $addon = Addon::find($this->addon_id);

        $updateRecord = $this->except(['search','formMode','updateMode','addon_id','image','originalImage','page','paginators']);

        $addon->update($updateRecord);
  
        $this->formMode = false;
        $this->updateMode = false;
  
        $this->flash('success',trans('messages.edit_success_message'));
        $this->resetInputFields();
        return redirect()->route('admin.addon');

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

    public function deleteConfirm($event){
        $deleteId = $event['data']['inputAttributes']['deleteId'];
        $model = Addon::find($deleteId);
        $model->delete();
        $this->alert('success', trans('messages.delete_success_message'));
    }

    public function show($id){
        $this->addon_id = $id;
        $this->formMode = false;
        $this->viewMode = true;
    }

    private function resetInputFields(){
        $this->title = '';
        $this->price = '';
        $this->credit = '';
        $this->status = 1;
    }

    public function cancel(){
        $this->formMode = false;
        $this->updateMode = false;
        $this->viewMode = false;

    }

    public function toggle($id){
        $this->confirm('Are you sure you want to change the status?', [
            'toast' => false,
            'position' => 'center',
            'confirmButtonText' => 'Yes Confirm!',
            'cancelButtonText' => 'No Cancel!',
            'onConfirmed' => 'confirmedToggleAction',
            'onCancelled' => function () {
                // Do nothing or perform any desired action
            },
            'inputAttributes' => ['packageId' => $id],
        ]);
    }

    public function confirmedToggleAction($event)
    {
        $packageId = $event['data']['inputAttributes']['packageId'];
        $model = Addon::find($packageId);
        $model->update(['status' => !$model->status]);
        $this->alert('success', trans('messages.change_status_success_message'));
    }

    public function changeStatus($statusVal){
        $this->status = (!$statusVal) ? 1 : 0;
    }
    
    public function initializePlugins(){
        $this->dispatchBrowserEvent('loadPlugins');
    }


}
