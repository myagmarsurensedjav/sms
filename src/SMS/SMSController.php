<?php

namespace Selmonal\SMS;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Selmonal\SMS\Events\MessageWasSent;
use Selmonal\SMS\Message;
use InvalidArgumentException;

class SMSController extends Controller
{
    /**
     * @var Detector
     */
    protected $detector;

    /**
     * SMSController constructor.
     *
     * @param Detector $detector
     */
    public function __construct(Detector $detector)
    {
        $this->detector = $detector;
    }

    /**
     * Compose a new sms.
     * 
     * @return Response
     */
    public function compose()
    {
        return view('sms::compose');
    }

    /**
     * Clear the log messages.
     * 
     * @return Response
     */
    public function clear()
    {
        Message::truncate();

        return Redirect::route('sms.log');
    }

    /**
     * Display the logs.
     * 
     * @return Response
     */
    public function log()
    {
        $currentType = Input::has('type') ? Input::get('type') : null;
        $currentModalType = Input::has('modaltype') ? Input::get('modaltype') : null;
        $dates = Input::has('date') ? strtodates(Input::get('date')): null ;
        $date = Input::get('date');
        $text = Input::has('text') ? Input::get('text') : null;
        $number = Input::has('number') ? Input::get('number') : null;
        $messages = Message::orderBy('id', 'desc')
        ->where(function ($query) use ($currentType) {
            if ($currentType) {
                $query->whereType($currentType);
            }
        })
        ->orWhere(
            function ($query) use ($dates,$text,$number,$currentModalType) {
                if ($currentModalType) {
                $query->whereType($currentModalType);
               }
                if ($dates) {
                    $query->where('sent_at', '>=', $dates[0]->format('Y-m-d').' 00:00:00')
                    ->where('sent_at', '<=', $dates[1]->format('Y-m-d').' 23:59:59');
                }
                if($text){
                  $query->where('text', 'like','%'. $text . '%');
              }
              if($number){
                  $query->where('phone_number', 'like', $number . '%');
              }
          })
        ->paginate()
        ->appends(Input::all());
        return view('sms::log', compact('messages', 'currentType','currentModalType','date','text','number'));
    }

    /**
     * Send a sms.
     * 
     * @return Response
     */
    public function send()
    {
        try {

            $numbers = $this->resolveNumbers($this->getPhoneNumbers());

            $this->validateNumbers($numbers);

            foreach($numbers as $number) {
                SMSFacade::send($number, Input::get('text'), 'custom');
            }

            return Redirect::route('sms.log');

        } catch(InvalidArgumentException $exception) {


            return Redirect::back()
            ->withInput()
            ->with('error_message', $exception->getMessage());

        }
    }

    public function getPhoneNumbers()
    {
        if(Input::hasFile('number_file')) {

            $file = Input::file('number_file');

            $content = file_get_contents($file->getPathname());

            return preg_split('/\n/', $content);
        }

        return explode(',', Input::get('phone_number'));
    }

    /**
     * Харилцагчын утасны дугаар.
     *
     * @param $numbers
     * @return void
     */
    private function validateNumbers($numbers)
    {
        foreach($numbers as $number) {
            if(! $this->detector->find($number)) {
                throw new InvalidArgumentException("{$number} утасны дугаар буруу байна.");
            }
        }
    }

    /**
     * @param $numbers
     */
    private function resolveNumbers($numbers)
    {
        return array_map(function($number) { return trim($number); }, $numbers);
    }
}
