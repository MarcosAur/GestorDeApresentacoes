<?php

namespace App\Livewire\Admin;

use App\Models\Presentation;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Check-in / Credenciamento')]
class CheckinScanner extends Component
{
    public $manualHash = '';
    public $isCameraActive = false;

    public function toggleCamera()
    {
        $this->isCameraActive = !$this->isCameraActive;
        $this->dispatch($this->isCameraActive ? 'camera-activated' : 'camera-deactivated');
    }

    public function processCheckin($hash)
    {
        \Illuminate\Support\Facades\Log::info('Checking in hash: ' . $hash);
        if (empty($hash)) {
            return;
        }

        $presentation = Presentation::where('qr_code_hash', $hash)->first();

        if (!$presentation) {
            \Illuminate\Support\Facades\Log::warning('Presentation not found for hash: ' . $hash);
            session()->flash('error', 'QR Code inválido ou não encontrado.');
            $this->manualHash = '';
            return;
        }

        if ($presentation->checkin_realizado) {
            session()->flash('warning', "Check-in já realizado para: {$presentation->competitor->name}");
            $this->manualHash = '';
            return;
        }

        $presentation->update(['checkin_realizado' => true]);

        session()->flash('success', "Check-in confirmado: {$presentation->competitor->name} ({$presentation->work_title})");
        
        $this->manualHash = '';
        
        $this->dispatch('checkin-completed');
    }

    public function render()
    {
        return view('livewire.admin.checkin-scanner');
    }
}
