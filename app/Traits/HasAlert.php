<?php

namespace App\Traits;

use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

trait HasAlert
{
  public function alertSuccess($title, $text = null)
  {
    LivewireAlert::title($title)
      ->text($text)
      ->success()
      ->show();
  }

  public function alertError($title, $text = null)
  {
    LivewireAlert::title($title)
      ->text($text)
      ->error()
      ->show();
  }

  public function alertInfo($title, $text = null)
  {
    LivewireAlert::title($title)
      ->text($text)
      ->info()
      ->show();
  }

  public function alertWarning($title, $text = null)
  {
    LivewireAlert::title($title)
      ->text($text)
      ->warning()
      ->show();
  }

  public function alertConfirm($title, $text, $method, $params = [])
  {
    LivewireAlert::title($title)
      ->text($text)
      ->asConfirm()
      ->onConfirm($method, $params)
      ->show();
  }
}
