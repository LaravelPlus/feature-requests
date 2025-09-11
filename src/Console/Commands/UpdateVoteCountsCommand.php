<?php

declare(strict_types=1);

namespace LaravelPlus\FeatureRequests\Console\Commands;

use Illuminate\Console\Command;
use LaravelPlus\FeatureRequests\Models\FeatureRequest;

class UpdateVoteCountsCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'feature-requests:update-vote-counts {--dry-run : Show what would be updated without making changes}';

    /**
     * The console command description.
     */
    protected $description = 'Update vote counts for all feature requests to ensure they are in sync with actual votes';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');
        
        $this->info('Updating vote counts for all feature requests...');
        
        if ($isDryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
        }
        
        $featureRequests = FeatureRequest::all();
        $updatedCount = 0;
        
        $this->info("Found {$featureRequests->count()} feature requests");
        
        $this->newLine();
        $this->table(
            ['ID', 'Title', 'Old Counts', 'New Counts', 'Status'],
            $featureRequests->map(function (FeatureRequest $fr) use ($isDryRun, &$updatedCount) {
                $oldVoteCount = $fr->vote_count;
                $oldUpVotes = $fr->up_votes;
                $oldDownVotes = $fr->down_votes;
                
                // Calculate what the new counts should be
                $upVotes = $fr->votes()->where('vote_type', 'up')->count();
                $downVotes = $fr->votes()->where('vote_type', 'down')->count();
                $totalVotes = $upVotes + $downVotes;
                
                $needsUpdate = $oldVoteCount != $totalVotes || $oldUpVotes != $upVotes || $oldDownVotes != $downVotes;
                
                if ($needsUpdate) {
                    if (!$isDryRun) {
                        $fr->updateVoteCount();
                        $fr->refresh();
                    }
                    $updatedCount++;
                    $status = $isDryRun ? 'Would Update' : 'Updated';
                } else {
                    $status = 'OK';
                }
                
                return [
                    $fr->id,
                    \Str::limit($fr->title, 30),
                    "Total: {$oldVoteCount}, Up: {$oldUpVotes}, Down: {$oldDownVotes}",
                    "Total: {$totalVotes}, Up: {$upVotes}, Down: {$downVotes}",
                    $status
                ];
            })->toArray()
        );
        
        $this->newLine();
        
        if ($isDryRun) {
            $this->info("Would update {$updatedCount} feature requests");
        } else {
            $this->info("Updated {$updatedCount} feature requests");
        }
        
        return Command::SUCCESS;
    }
}
