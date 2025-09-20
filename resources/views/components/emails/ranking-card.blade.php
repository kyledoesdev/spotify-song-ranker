<div style="background-color: white; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border-radius: 0.5rem; margin: 1rem 0; padding: 1rem; border: 1px solid #e5e7eb;">
    <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
        <tr>
            <td width="120" style="vertical-align: top; padding-right: 16px;">
                <img 
                    src="{{ $ranking->artist->artist_img }}"
                    width="120" 
                    height="120" 
                    style="border-radius: 8px; border: 1px solid #27272a; display: block;"
                    alt="{{ $ranking->artist->artist_name }}"
                >
                <div style="margin-top: 8px;">
                    <a 
                        href="{{ route('ranking', $ranking->getKey()) }}"
                        style="display: inline-flex; align-items: center; gap: 4px; border-bottom: 2px solid #06D6A0; padding-bottom: 2px; text-decoration: none; color: #06D6A0; font-size: 12px;"
                    >
                        🎵 Spotify
                    </a>
                </div>
            </td>
            <td style="vertical-align: top;">
                <h3 style="margin: 0 0 12px 0; font-size: 18px; font-weight: bold; color: #1f2937;">
                    {{ Str::limit($ranking->name, 40) }}
                </h3>
                
                <div style="margin-bottom: 8px; font-size: 14px; color: #6b7280;">
                    <strong>🎵 Artist:</strong> {{ $ranking->artist->artist_name }}
                </div>
                
                @if($ranking->songs && $ranking->songs->isNotEmpty())
                <div style="margin-bottom: 8px; font-size: 14px; color: #6b7280;">
                    <strong>⭐ #1 Song:</strong> {{ Str::limit($ranking->songs[0]->title, 30) }}
                </div>
                @endif
                
                <div style="margin-bottom: 8px; font-size: 14px; color: #6b7280;">
                    <strong>#️⃣ Songs:</strong> {{ $ranking->songs_count }} songs ranked
                </div>
                
                <div style="margin-bottom: 8px; font-size: 14px; color: #6b7280;">
                    <strong>👤 Created by:</strong>
                    <a href="{{ route('profile', ['id' => $ranking->user->spotify_id]) }}" target="_blank">
                        {{ $ranking->user->name }}
                    </a>
                </div>
                
                <div style="margin-bottom: 12px; font-size: 14px; color: #6b7280;">
                    <strong>🕒 Completed:</strong> {{ Carbon\Carbon::parse($ranking->completed_at)->diffForHumans() }}
                </div>
                
                <a 
                    href="{{ route('ranking', $ranking->getKey()) }}"
                    style="display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-size: 14px;"
                >
                    View Ranking →
                </a>
            </td>
        </tr>
    </table>
</div>