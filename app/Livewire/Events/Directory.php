<?php

namespace App\Livewire\Events;

use App\Models\Event;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Url;
use Livewire\Component;

class Directory extends Component
{
    private const ITEMS_PER_PAGE = 6;

    #[Url]
    public string $search = '';

    #[Url]
    public string $filter = 'all';

    public int $page = 1;

    public function updatedSearch(): void
    {
        $this->page = 1;
    }

    public function setFilter(string $filter): void
    {
        $this->filter = $filter;
        $this->page = 1;
    }

    public function gotoPage(int $page): void
    {
        $this->page = $page;
    }

    public function previousPage(): void
    {
        $this->page = max(1, $this->page - 1);
    }

    public function nextPage(): void
    {
        $this->page = $this->page + 1;
    }

    public function render()
    {
        $today = today();

        $events = Event::withCount('approvedRegistrations')->get()->map(function (Event $event) use ($today) {
            $eventDay = $event->date->copy()->startOfDay();
            $status = $eventDay->eq($today) ? 'current' : ($eventDay->gt($today) ? 'upcoming' : 'past');

            $priceNormal = $event->price_normal ?: ($event->type === 'Workshop' ? 'Free' : '₹1,499');
            $priceVerified = $event->price_verified ?: ($event->type === 'Workshop' ? 'Free' : '₹1,499');

            return (object) [
                'model' => $event,
                'status' => $status,
                'monthShort' => strtoupper($event->date->format('M')),
                'dayNum' => $event->date->format('d'),
                'yearNum' => $event->date->format('Y'),
                'dateLabel' => $event->date->format('M j, Y'),
                'attendees' => $event->approved_registrations_count,
                'priceNormal' => $priceNormal,
                'priceVerified' => $priceVerified,
                'image' => media_url($event->image),
            ];
        });

        $search = mb_strtolower($this->search);
        $filtered = $events->filter(function ($e) use ($search) {
            $matchesFilter = match ($this->filter) {
                'current' => in_array($e->status, ['current', 'upcoming'], true),
                'upcoming' => $e->status === 'upcoming',
                'past' => $e->status === 'past',
                default => true,
            };

            $matchesSearch = $search === '' ||
                str_contains(mb_strtolower($e->model->title), $search) ||
                str_contains(mb_strtolower((string) $e->model->type), $search);

            return $matchesFilter && $matchesSearch;
        })->values();

        $statusOrder = ['current' => 0, 'upcoming' => 1, 'past' => 2];
        $sorted = $filtered->sort(function ($a, $b) use ($statusOrder) {
            if ($statusOrder[$a->status] !== $statusOrder[$b->status]) {
                return $statusOrder[$a->status] <=> $statusOrder[$b->status];
            }

            return $a->status === 'past'
                ? $b->model->date <=> $a->model->date
                : $a->model->date <=> $b->model->date;
        })->values();

        $total = $sorted->count();
        $lastPage = max(1, (int) ceil($total / self::ITEMS_PER_PAGE));
        $this->page = min($this->page, $lastPage);

        $pageItems = $sorted->slice(($this->page - 1) * self::ITEMS_PER_PAGE, self::ITEMS_PER_PAGE)->values();

        $paginator = new LengthAwarePaginator($pageItems, $total, self::ITEMS_PER_PAGE, $this->page);

        return view('livewire.events.directory', [
            'events' => $paginator,
            'isVerifiedMember' => auth()->check() && optional(auth()->user()->business)->status === 'approved',
        ]);
    }
}
