@foreach($data['images'] as $image)
<div class="col-sm-3 col-xs-6 text-center">
  @if ($image['type'] == 'directory')
  <div class="text-center"><a href="{{ $image['href'] }}" class="directory" style="vertical-align: middle;"><i class="fa fa-folder fa-5x"></i></a></div>
  <label>
    <input type="checkbox" name="path[]" value="{{ $image['path'] }}" />
    {{ $image['name'] }}</label>
  @endif
  @if ($image['type'] == 'image')
  <a href="{{ $image['href'] }}" class="thumbnail"><img src="{{ $image['thumb'] }}" alt="{{ $image['name'] }}" title="{{ $image['name'] }}" /></a>
  <label>
    <input type="checkbox" name="path[]" value="{{ $image['path'] }}" />
    {{ $image['name'] }}</label>
  @endif
</div>
@endforeach