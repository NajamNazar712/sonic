<div class="comments dispute_comments_section">
    <div class="row ">
        <div class="col-12">
            @foreach($comments as $comment)
                <div class="comment-row border">
                    <p class="comment">{{$comment->comment}}</p>
                    <span class="comment-date"><b>Posted</b> at {{ $comment->created_at }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>