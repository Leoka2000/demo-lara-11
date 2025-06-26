<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\Cache;
use App\Events\MouseMoved;
use Livewire\Attributes\On;
use Livewire\Attributes\Locked;
use App\Events\SwitchFlipped;

new class extends Component {
     public $toggleSwitch = false;

    public function flipSwitch()
    {
        Cache::forever('toggleSwitch', $this->toggleSwitch);
        broadcast(new SwitchFlipped($this->toggleSwitch))->toOthers();
    }

}; ?>

<div x-data="{
    localToggle: @entangle('toggleSwitch'),}">


    <label for="toggleSwitch" class="flex items-center cursor-pointer">
        <div class="relative">
            <input type="checkbox" id="toggleSwitch" class="sr-only" x-model="localToggle"
                x-on:change="$wire.flipSwitch()">
            <div class="block h-8 bg-gray-600 rounded-full w-14"></div>
            <div class="absolute w-6 h-6 transition-transform duration-200 rounded-full left-1 top-1"
                x-bind:class="localToggle ? 'translate-x-full bg-green-400' : 'bg-white'">
            </div>
        </div>
    </label>
</div>