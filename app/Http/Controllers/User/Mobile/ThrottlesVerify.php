<?php

namespace App\Http\Controllers\User\Mobile;

use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;

trait ThrottlesVerify {
	protected function hasTooManyVerifyAttempts(Request $request) {
		return app(RateLimiter::class)->tooManyAttempts(
			$request->ip() . '|verify',
			$this->getMaxVerifyAttempts(),
			$this->getSendLockTime()
		);
	}

	protected function verifyAttempts(Request $request) {
		app(RateLimiter::class)->hit($request->ip() . '|verify');
		$this->hasTooManyVerifyAttempts($request);
	}

	protected function clearVerifyAttempts(Request $request) {
		app(RateLimiter::class)->clear($request->ip() . '|verify');
	}

	public function getAvailableVerifyIn(Request $request) {
		return app(RateLimiter::class)->availableIn($request->ip() . '|verify');
	}

	protected function getMaxVerifyAttempts() {
		return property_exists($this, 'maxVerifyAttempts') ? $this->maxVerifyAttempts : 5; // 验证短信的次数限制
	}

	protected function getVerifyLockTime() {
		return property_exists($this, 'verifyLockTime') ? $this->verifyLockTime : 0; // 验证短信的时间间隔
	}
}