<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CandidateController extends Controller
{
    public function create($id = null)
    {
        // Если ID передан, проверяем права доступа
        if ($id) {
            // Ищем по display_number вместо id
            $candidate = Candidate::where('display_number', $id)->firstOrFail();

            // Проверяем права доступа:
            // 1. Пользователь может редактировать только свои анкеты
            // 2. Администратор может редактировать любые анкеты
            if ($candidate->user_id !== auth()->id() && !auth()->user()->is_admin) {
                abort(403, 'У вас нет прав для редактирования этой анкеты.');
            }

            // Передаём реальный id кандидата для Livewire компонента
            $id = $candidate->id;
        }

        return view('candidate.form', ['candidateId' => $id]);
    }

    public function test()
    {
        return view('candidate.test');
    }
} 