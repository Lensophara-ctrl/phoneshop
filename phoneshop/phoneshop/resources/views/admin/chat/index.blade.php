@extends('layouts.app')

@section('content')
<style>
    .chat-sidebar {
        height: calc(100vh - 200px);
        overflow-y: auto;
        border-right: 1px solid var(--bs-border-color);
    }
    .chat-main {
        height: calc(100vh - 200px);
        display: flex;
        flex-direction: column;
    }
    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 1rem;
    }
    .chat-conversation-item {
        cursor: pointer;
        transition: all 0.2s;
        border-radius: 0.5rem;
        margin-bottom: 0.25rem;
    }
    .chat-conversation-item:hover {
        background: var(--bs-tertiary-bg);
    }
    .chat-conversation-item.active {
        background: var(--bs-primary-bg-subtle);
        border-left: 3px solid var(--bs-primary);
    }
    .chat-bubble {
        max-width: 80%;
        padding: 0.75rem 1rem;
        border-radius: 1rem;
        margin-bottom: 0.75rem;
        word-wrap: break-word;
    }
    .chat-bubble.customer {
        background: var(--bs-primary);
        color: white;
        border-bottom-left-radius: 0.25rem;
        align-self: flex-start;
    }
    .chat-bubble.admin {
        background: var(--bs-tertiary-bg);
        border-bottom-right-radius: 0.25rem;
        align-self: flex-end;
    }
    .status-badge {
        font-size: 0.7rem;
        padding: 0.2rem 0.5rem;
    }
    .unread-dot {
        width: 8px;
        height: 8px;
        background: var(--bs-danger);
        border-radius: 50%;
        display: inline-block;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">
        <i class="fa-solid fa-comments me-2"></i>Live Chat
    </h2>
    <span class="badge bg-danger rounded-pill" id="unreadBadge">{{ $unreadCount }} unread</span>
</div>

<div class="card shadow-sm border-0">
    <div class="row g-0">
        <div class="col-md-4">
            <div class="chat-sidebar p-2">
                <input type="text" class="form-control mb-3" id="searchChat" placeholder="Search conversations...">
                <div id="conversationsList">
                    @forelse($conversations as $conv)
                        <a href="{{ route('admin.chat.show', $conv) }}" class="text-decoration-none text-body chat-conversation-item p-3 d-flex align-items-center {{ request()->route('conversation')?->id == $conv->id ? 'active' : '' }}">
                            <div class="flex-shrink-0 me-3">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    {{ strtoupper(substr($conv->customer_name, 0, 1)) }}
                                </div>
                            </div>
                            <div class="flex-grow-1 min-width-0">
                                <div class="d-flex justify-content-between">
                                    <strong class="text-truncate">{{ $conv->customer_name }}</strong>
                                    <small class="text-muted ms-2">{{ $conv->messages->first()?->created_at?->diffForHumans() }}</small>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted text-truncate d-block">{{ $conv->messages->first()?->message ?? 'No messages' }}</small>
                                    @if($conv->messages->where('sender_type', 'customer')->where('is_read', false)->count())
                                        <span class="unread-dot ms-2"></span>
                                    @endif
                                </div>
                                <div>
                                    <span class="badge bg-{{ $conv->status == 'active' ? 'success' : ($conv->status == 'resolved' ? 'warning' : 'secondary') }} status-badge">{{ ucfirst($conv->status) }}</span>
                                </div>
                            </div>
                        </a>
                    @empty
                        <p class="text-muted text-center py-4">No conversations yet</p>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="chat-main">
                <div class="text-center text-muted py-5">
                    <i class="fa-solid fa-comment-dots fa-3x mb-3"></i>
                    <p>Select a conversation to view messages</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('searchChat')?.addEventListener('input', function() {
        const q = this.value.toLowerCase();
        document.querySelectorAll('.chat-conversation-item').forEach(el => {
            el.style.display = el.textContent.toLowerCase().includes(q) ? 'flex' : 'none';
        });
    });
</script>
@endsection
