<?php

namespace Selmonal\SMS;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Selmonal\SMS\Contracts\Message as MessageContract;

/**
 * @property string phone_number
 * @property string text
 * @property string vendor
 * @property string type
 * @property string status
 * @property Carbon sent_at
 */
class Message extends Model implements MessageContract
{
	/**
	 * The fillable fields.
	 * 
	 * @var array
	 */
	protected $fillable = [
		'phone_number', 'text', 'vendor', 'status', 'sent_at', 'type'
	];

	/**
	 * Get the table name.
	 * 
	 * @return string
	 */
	public function getTable()
	{
		return Config::get('sms.table') ? 
			Config::get('sms.table') : 'messages';
	}

	/**
	 * Set the message as sent.
	 *
	 * @return $this
	 */
	public function setAsSent()
	{
		$this->status = 'sent';
		$this->sent_at = Carbon::now();
		return $this;
	}

	/**
	 * @param Builder $builder
	 * @return Builder
	 */
	public function scopeToday($builder)
	{
		return $builder->where(\DB::raw('date(sent_at)'), date('Y-m-d'));
	}

	/**
	 * Count the messages that has sent today.
	 *
	 * @param null $type
	 * @return int
	 */
	public static function countToday($type = null)
	{
		return static::where('status', 'sent')
			->whereType($type)
			->where(\DB::raw('date(sent_at)'), date('Y-m-d'))
			->count();
	}

	/**
	 * Count the messages that has sent yesterday.
	 *
	 * @param null $type
	 * @return int
	 */
	public static function countYesterday($type = null)
	{
		return static::where('status', 'sent')
			->whereType($type)
			->where(\DB::raw('date(sent_at)'), Carbon::yesterday()->format('Y-m-d'))
			->count();
	}

	/**
	 * Count the messages that has sent this month.
	 *
	 * @param null $type
	 * @return int
	 */
	public static function countThisMonth($type = null)
	{
		return static::where('status', 'sent')
			->whereType($type)
			->where('sent_at', '>=', date('Y-m-01'))
			->count();
	}

	/**
	 * Count the messages that has sent last month.
	 *
	 * @param null $type
	 * @return int
	 */
	public static function countLastMonth($type = null)
	{
		return static::where('status', 'sent')
			->whereType($type)
			->where('sent_at', '<', date('Y-m-01'))
			->where('sent_at', '>=', Carbon::now()->subMonth()->format('Y-m-01'))
			->count();
	}

	/**
	 * Count the all messages that has sent.
	 *
	 * @return integer
	 */
	public static function count()
	{
		return static::today()->count();
	}

	/**
	 * @return string
	 */
	public function getPhoneNumber()
	{
		return $this->phone_number;
	}

	/**
	 * @return string
	 */
	public function getText()
	{
		return $this->text;
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}
}