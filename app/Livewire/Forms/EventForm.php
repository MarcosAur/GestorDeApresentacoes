<?php

namespace App\Livewire\Forms;

use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Form;

class EventForm extends Form
{
    public ?Event $event = null;

    #[Validate('required|string|max:255')]
    public $name = '';

    #[Validate('required|date')]
    public $event_date = '';

    #[Validate('nullable|string')]
    public $description = '';

    public function setEvent(Event $event)
    {
        $this->event = $event;
        $this->name = $event->name;
        $this->event_date = $event->event_date;
        $this->description = $event->description;
    }

    public function save()
    {
        $this->validate();

        if ($this->event) {
            $this->event->update($this->all());
            return 'Evento atualizado com sucesso!';
        }

        Event::create(array_merge($this->all(), [
            'admin_id' => Auth::id(),
        ]));

        return 'Evento criado com sucesso!';
    }
}
