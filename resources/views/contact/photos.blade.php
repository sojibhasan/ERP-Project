@if(isset($contact->image) && $contact->image!=null )
<strong>Image</strong><br>
<p class="text-muted">
    <img src="{{asset('/uploads/media/'.$contact->image)}} " width="50" height="50">
</p>
@endif
<br>
@if(isset($contact->signature) && $contact->signature!=null )
<strong>Signature</strong><br>
<p class="text-muted">
    <img src="{{asset('/uploads/media/'.$contact->signature)}}" width="50" height="50">
</p>
@endif