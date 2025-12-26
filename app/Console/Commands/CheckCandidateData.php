<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Candidate;

class CheckCandidateData extends Command
{
    protected $signature = 'candidate:check {id}';
    protected $description = 'Check candidate data in database';

    public function handle()
    {
        $id = $this->argument('id');
        $candidate = Candidate::find($id);

        if (!$candidate) {
            $this->error("Candidate with ID {$id} not found");
            return 1;
        }

        $this->info("=== Candidate ID: {$candidate->id} ===");
        $this->info("Full Name: {$candidate->full_name}");
        $this->info("Email: {$candidate->email}");
        $this->line("");

        $this->info("=== Employer Requirements Field ===");
        $this->line("Value: " . ($candidate->employer_requirements ?? 'NULL'));
        $this->line("Length: " . strlen($candidate->employer_requirements ?? ''));
        $this->line("Is Empty: " . (empty($candidate->employer_requirements) ? 'YES' : 'NO'));
        $this->line("");

        $this->info("=== Other Step 3 Fields ===");
        $this->line("Desired Position: " . ($candidate->desired_position ?? 'NULL'));
        $this->line("Activity Sphere: " . ($candidate->activity_sphere ?? 'NULL'));
        $this->line("Computer Skills: " . ($candidate->computer_skills ?? 'NULL'));
        $this->line("");

        $this->info("=== Raw Database Value ===");
        $raw = \DB::table('candidates')
            ->where('id', $id)
            ->select('employer_requirements')
            ->first();

        if ($raw) {
            $this->line("Raw employer_requirements: " . ($raw->employer_requirements ?? 'NULL'));
        }

        return 0;
    }
}
