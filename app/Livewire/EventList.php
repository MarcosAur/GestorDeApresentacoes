<?php

namespace App\Livewire;

use App\Livewire\Forms\EventForm;
use App\Models\Event;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class EventList extends Component
{
    use WithPagination;

    public EventForm $form;
    public $showModal = false;
    public $editingEvent = null;

    public function openModal($eventId = null)
    {
        $this->resetValidation();
        $this->form->reset();

        if ($eventId) {
            $this->editingEvent = Event::findOrFail($eventId);
            $this->form->setEvent($this->editingEvent);
        } else {
            $this->editingEvent = null;
        }

        $this->showModal = true;
    }

    public function save()
    {
        $message = $this->form->save();
        $this->dispatch('notify', $message);
        $this->showModal = false;
    }

    public function delete($eventId)
    {
        $event = Event::findOrFail($eventId);
        
        if ($event->contests()->exists()) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Não é possível deletar um evento que possui concursos associados.']);
            return;
        }

        $event->delete();
        $this->dispatch('notify', 'Evento deletado com sucesso!');
    }

    public function render()
    {
        return view('livewire.event-list', [
            'events' => Event::latest()->paginate(10)
        ]);
    }
}
