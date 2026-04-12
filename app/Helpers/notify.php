<?php

if (!function_exists('notify')) {
    /**
     * Helper para disparar notificaciones Notiflix.
     * Soporta tipos: 'success', 'failure', 'warning', 'info'
     */
    function notify(string $type, string $title, ?string $message = null)
    {
        session()->flash('notiflix', [
            'type'    => $type,
            'title'   => $title,
            'message' => $message
        ]);
    }
}
