<div class="comments dispute_comments_section">
    <div class="row ">
        @foreach($comments as $comment)
            <div class="col-12">
                <p class="comment">{{$comment->comment}}</p>
                <hr>
                <span class="comment-date"><b>Posted</b> at {{\Carbon\Carbon::parse($comment->created_at)->format('d/m/Y H:i:s A')}}</span>
            </div>
        @endforeach
    </div>
</div>