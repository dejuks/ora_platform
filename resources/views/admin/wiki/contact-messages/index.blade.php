@extends('layouts.admin')

@section('title', 'Contact Messages')

@section('content')
    <div class="container-fluid py-4">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4 mb-0">
                Contact Messages
                @if($unreadCount > 0)
                    <span class="badge bg-danger">{{ $unreadCount }} unread</span>
                @endif
            </h1>

            <div class="btn-group btn-group-sm">
                <a href="{{ route('admin.wiki.contact-messages.index') }}"
                   class="btn btn-outline-secondary {{ request('filter') ? '' : 'active' }}">All</a>
                <a href="{{ route('admin.wiki.contact-messages.index', ['filter' => 'unread']) }}"
                   class="btn btn-outline-secondary {{ request('filter') === 'unread' ? 'active' : '' }}">Unread</a>
            </div>
        </div>

        @if(session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                <tr>
                    <th></th>
                    <th>From</th>
                    <th>Subject</th>
                    <th>Received</th>
                    <th class="text-end">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($messages as $message)
                    <tr class="{{ $message->read_at ? '' : 'fw-semibold' }}">
                        <td>
                            @unless($message->read_at)
                                <span class="badge bg-primary rounded-circle p-1">&nbsp;</span>
                            @endunless
                        </td>
                        <td>
                            {{ $message->name }}
                            <div class="small text-muted fw-normal">{{ $message->email }}</div>
                        </td>
                        <td>
                            <a href="{{ route('admin.wiki.contact-messages.show', $message) }}">
                                {{ $message->subject }}
                            </a>
                        </td>
                        <td class="small text-muted fw-normal">{{ $message->created_at->format('d M Y, H:i') }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.wiki.contact-messages.show', $message) }}"
                               class="btn btn-sm btn-outline-primary" title="View">
                                <i class="bi bi-envelope-open"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.wiki.contact-messages.destroy', $message) }}"
                                  class="d-inline" onsubmit="return confirm('Delete this message?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No messages.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{ $messages->links() }}
    </div>
@endsection
