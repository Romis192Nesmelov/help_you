<a href="mailto:{{ $email }}">{!! isset($icon) && $icon ? '<i class="'.$icon.(isset($addClass) && $addClass ? ' '.$addClass : '').'"></i>' : $email !!}</a>
