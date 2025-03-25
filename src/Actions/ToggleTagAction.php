<?php

namespace Marshmallow\NovaActivity\Actions;

use Illuminate\Support\Arr;
use Marshmallow\NovaActivity\Models\NovaActivity;

class ToggleTagAction
{
    protected $icon;
    protected $color = 'red';
    protected $background_color = '#fff';
    protected $border_color = 'red';

    public static function make(string $name, string $key): self
    {
        return new self($name, $key);
    }

    protected function __construct(protected string $name, protected string $key)
    {
        //
    }

    public function color(string $color): self
    {
        $this->color = $color;
        return $this;
    }

    public function backgroundColor(string $background): self
    {
        $this->background_color = $background;
        return $this;
    }

    public function borderColor(string $border): self
    {
        $this->border_color = $border;
        return $this;
    }

    public function icon(string $icon): self
    {
        $this->icon = $icon;
        return $this;
    }

    public static function handle(NovaActivity $comment, array $action)
    {
        $tags = Arr::get($comment->meta, 'tags', []);
        $tag_key = Arr::get($action, 'key');
        if (Arr::has($tags, $tag_key)) {
            unset($tags[$tag_key]);
        } else {
            $tags[$tag_key] = $action;
        }

        $comment->update([
            'meta' => array_merge($comment->meta, [
                'tags' => $tags,
            ]),
        ]);
    }

    public function toArray()
    {
        return [
            'class' => get_class($this),
            'key' => $this->key,
            'name' => $this->name,
            'color' => $this->color,
            'background_color' => $this->background_color,
            'icon' => $this->icon,
        ];
    }
}
