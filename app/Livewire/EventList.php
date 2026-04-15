<?php

namespace App\Livewire;

use App\Models\Event;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class EventList extends Component
{
    use WithPagination;

    public $showModal = false;
    public $editingEvent = null;

    public $name;
    public $event_date;
    public $description;

    protected $rules = [
        'name' => 'required|string|max:255',
        'event_date' => 'required|date',
        'description' => 'nullable|string',
    ];

    public function openModal($eventId = null)
    {
        $this->resetValidation();
        $this->reset(['name', 'event_date', 'description']);

        if ($eventId) {
            $this->editingEvent = Event::findOrFail($eventId);
            $this->name = $this->editingEvent->name;
            $this->event_date = $this->editingEvent->event_date;
            $this->description = $this->editingEvent->description;
        } else {
            $this->editingEvent = null;
        }

        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        if ($this->editingEvent) {
            $this->editingEvent->update([
                'name' => $this->name,
                'event_date' => $this->event_date,
                'description' => $this->description,
            ]);
            $this->dispatch('notify', 'Evento atualizado com sucesso!');
        } else {
            Event::create([
                'admin_id' => Auth::id(),
                'name' => $this->name,
                'event_date' => $this->event_date,
                'description' => $this->description,
            ]);
            $this->dispatch('notify', 'Evento criado com sucesso!');
        }

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
