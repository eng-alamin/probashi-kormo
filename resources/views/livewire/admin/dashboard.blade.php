@use('App\Enums\ActivityEvent')
@use('App\Enums\ApplicationStatus')
@use('App\Enums\InquiryStatus')
<div>
  <div class="page-head">
    <div><h2>Dashboard</h2><p>Overview of applications, jobs and messages</p></div>
  </div>

  @if ($isEmpty)
    <div class="glass panel">
      <div class="state-block">
        <div class="state-icon" aria-hidden="true"><i class="bi bi-grid-1x2-fill"></i></div>
        <h6>Welcome</h6>
        <p>Use the menu to open the sections you have access to.</p>
      </div>
    </div>
  @endif

  @if (count($cards) > 0)
    <div class="grid-4 mb-4" style="grid-template-columns:repeat(auto-fit,minmax(210px,1fr));">
      @foreach ($cards as $card)
        <div class="glass stat-card" wire:key="card-{{ $card['label'] }}">
          <div class="stat-icon" style="{{ $card['style'] }}" aria-hidden="true"><i class="bi {{ $card['icon'] }}"></i></div>
          <div class="label">{{ $card['label'] }}</div><div class="value">{{ $card['value'] }}</div>
          <a href="{{ $card['url'] }}" class="delta">{{ $card['note'] }}</a>
        </div>
      @endforeach
    </div>
  @endif

  @if ($recentApplications !== null)
    <div class="grid-2 mb-4">
      <div class="glass panel">
        <div class="panel-title">
          <h5>Recent applications</h5>
          <a href="{{ route('admin.applications.index') }}" class="btn btn-ghost btn-sm">View all</a>
        </div>
        @if ($recentApplications->isEmpty())
          <p class="cell-sub">No applications yet. They will appear here when someone applies on the website.</p>
        @else
          <div class="table-wrap">
            <table class="responsive-table">
              <thead>
                <tr>
                  <th scope="col">Applicant</th>
                  <th scope="col">Applied for</th>
                  <th scope="col">Status</th>
                  <th scope="col">Date</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($recentApplications as $app)
                  @php($pill = match ($app->status) {
                    ApplicationStatus::New => 'status-trial',
                    ApplicationStatus::Contacted => 'status-suspended',
                    ApplicationStatus::Rejected => 'status-past',
                    default => 'status-active',
                  })
                  <tr wire:key="dash-app-{{ $app->id }}">
                    <td class="cell-main-td">
                      <div class="cell-main">
                        <span class="cell-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($app->full_name, 0, 1)) }}</span>
                        <div>
                          <div class="cell-title"><a href="{{ route('admin.applications.show', $app) }}">{{ $app->full_name }}</a></div>
                          <div class="cell-sub">{{ $app->phone }}</div>
                        </div>
                      </div>
                    </td>
                    <td data-label="Applied for">{{ $app->jobPost?->title ?? $app->country?->name ?? '—' }}</td>
                    <td data-label="Status"><span class="status-pill {{ $pill }}">{{ $app->status->label() }}</span></td>
                    <td data-label="Date">{{ $app->created_at->format('d M Y') }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @endif
      </div>

      <div class="glass panel">
        <div class="panel-title"><h5>Applications by status</h5></div>
        @foreach ($statusRows as $row)
          <div style="margin-bottom:14px;" wire:key="dash-status-{{ $row['key'] }}">
            <div class="detail-row" style="border-bottom:none; padding:0 0 6px;"><span class="k">{{ $row['label'] }}</span><span>{{ $row['count'] }}</span></div>
            <div class="progress" role="progressbar" aria-label="{{ $row['label'] }}" aria-valuenow="{{ $row['percent'] }}" aria-valuemin="0" aria-valuemax="100"><div class="progress-bar" style="width:{{ $row['percent'] }}%;"></div></div>
          </div>
        @endforeach

        <div class="panel-title" style="margin-top:22px;"><h5>Last 7 days</h5></div>
        <div class="mini-bar" role="img" aria-label="Applications per day, last 7 days: {{ collect($weekBars)->map(fn ($b) => $b['label'].' '.$b['count'])->implode(', ') }}">
          @foreach ($weekBars as $bar)
            <span style="height:{{ $bar['height'] }}%;" title="{{ $bar['title'] }}"></span>
          @endforeach
        </div>
        <div style="display:flex; gap:5px; margin-top:6px;" aria-hidden="true">
          @foreach ($weekBars as $bar)
            <span class="cell-sub" style="flex:1; text-align:center;">{{ $bar['label'] }}</span>
          @endforeach
        </div>
      </div>
    </div>
  @endif

  @if ($recentInquiries !== null || $recentActivity !== null)
    <div class="grid-2 mb-4">
      @if ($recentInquiries !== null)
        <div class="glass panel">
          <div class="panel-title">
            <h5>Recent messages</h5>
            <a href="{{ route('admin.inquiries.index') }}" class="btn btn-ghost btn-sm">View all</a>
          </div>
          @if ($recentInquiries->isEmpty())
            <p class="cell-sub">No messages yet. They will appear here when someone uses the contact page.</p>
          @else
            @foreach ($recentInquiries as $inq)
              @php($pill = match ($inq->status) {
                InquiryStatus::New => 'status-trial',
                InquiryStatus::Read => 'status-suspended',
                default => 'status-active',
              })
              <div class="detail-row" wire:key="dash-inq-{{ $inq->id }}">
                <span>
                  <a href="{{ route('admin.inquiries.show', $inq) }}"><strong>{{ $inq->full_name }}</strong></a>
                  <span class="cell-sub" style="display:block;">{{ \Illuminate\Support\Str::limit($inq->message, 70) }}</span>
                </span>
                <span style="text-align:right;">
                  <span class="status-pill {{ $pill }}">{{ $inq->status->label() }}</span>
                  <span class="cell-sub" style="display:block; margin-top:4px;">{{ $inq->created_at->diffForHumans() }}</span>
                </span>
              </div>
            @endforeach
          @endif
        </div>
      @endif

      @if ($recentActivity !== null)
        <div class="glass panel">
          <div class="panel-title">
            <h5>Recent activity</h5>
            <a href="{{ route('admin.logs.index') }}" class="btn btn-ghost btn-sm">View all</a>
          </div>
          @if ($recentActivity->isEmpty())
            <p class="cell-sub">No activity yet. Changes made in the admin panel will appear here.</p>
          @else
            @foreach ($recentActivity as $log)
              @php($type = ActivityEvent::tryFrom($log->event))
              <div class="detail-row" wire:key="dash-log-{{ $log->id }}">
                <span>
                  <strong>{{ $log->user?->name ?? $log->actor_name ?? 'System' }}</strong>
                  <span class="cell-sub" style="display:block;">{{ $log->description }}</span>
                </span>
                <span style="text-align:right;">
                  <span class="status-pill {{ $type?->pill() ?? 'status-suspended' }}">{{ $type?->label() ?? ucfirst(str_replace('_', ' ', $log->event)) }}</span>
                  <span class="cell-sub" style="display:block; margin-top:4px;">{{ $log->created_at->diffForHumans() }}</span>
                </span>
              </div>
            @endforeach
          @endif
        </div>
      @endif
    </div>
  @endif

  @if (count($environment) > 0)
    <div class="glass panel">
      <div class="panel-title"><h5>Environment (local only)</h5></div>
      <div class="table-wrap">
        <table class="responsive-table">
          <thead><tr><th scope="col">Item</th><th scope="col">Value</th></tr></thead>
          <tbody>
            @foreach ($environment as $name => $value)
              <tr><td data-label="Item">{{ $name }}</td><td data-label="Value">{{ $value }}</td></tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  @endif
</div>
