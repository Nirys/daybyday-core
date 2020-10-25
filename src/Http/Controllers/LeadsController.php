<?php
namespace SpiritSystems\DayByDay\Core\Http\Controllers;

use SpiritSystems\DayByDay\Core\Http\Requests\StoreLeadRequest;
use App\Models\Client;
use App\Models\Lead;
use App\Models\Status;
use App\Models\User;
use Ramsey\Uuid\Uuid;
use Carbon;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Requests\Lead\UpdateLeadFollowUpRequest;
use App\Models\Document;
use App\Models\Integration;
use App\Services\Storage\GetStorageProvider;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;

class LeadsController extends DayByDayController {

    const CREATED = 'created';
    const UPDATED_STATUS = 'updated_status';
    const UPDATED_DEADLINE = 'updated_deadline';
    const UPDATED_ASSIGN = 'updated_assign';

    public function __construct()
    {
        $this->middleware('lead.create', ['only' => ['create']]);
        $this->middleware('lead.assigned', ['only' => ['updateAssign']]);
        $this->middleware('lead.update.status', ['only' => ['updateStatus']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function unqualified()
    {
        return view('leads.unqualified')->withStatuses(Status::typeOfLead()->get());
    }

    /**
     * Data for Data tables
     * @return mixed
     */
    public function unqualifiedLeads()
    {
        $status_id = Status::typeOfLead()->where('title', ['like' => '%Closed%'])->get()->pluck('id')->toArray();
        $leads = Lead::isNotQualified()
            ->whereNotIn('status_id', $status_id)
            ->with(['user', 'creator', 'client.primaryContact'])->get();

        $leads->map(function ($item) {
            return [$item['visible_deadline_date'] = $item['deadline']->format(carbonDate()), $item["visible_deadline_time"] = $item['deadline']->format(carbonTime())];
        });
        return $leads->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($client_external_id = null)
    {
        $client =  Client::whereExternalId($client_external_id);

        return view('leads.create')
            ->withFilesystemIntegration(false)
            ->withUsers(User::with(['department'])->get()->pluck('nameAndDepartmentEagerLoading', 'id'))
            ->withClients(Client::pluck('company_name', 'external_id'))
            ->withClient($client ?: null)
            ->withStatuses(Status::typeOfLead()->pluck('title', 'id'));
    }

    public function edit($external_id){
        return view('leads.edit')
            ->withLead($this->findByExternalId($external_id))
            ->withUsers(User::with(['department'])->get()->pluck('nameAndDepartmentEagerLoading', 'id'))
            ->withClients(Client::pluck('company_name', 'external_id'))
            ->withCompanyname(Setting::first()->company)
            ->withStatuses(Status::typeOfLead()->pluck('title', 'id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreLeadRequest|Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreLeadRequest $request)
    {
        if ($request->client_external_id) {
            $client = Client::whereExternalId($request->client_external_id);
        }

        $lead = Lead::create(
            [
                'title' => $request->title,
                'description' => clean($request->description),
                'user_assigned_id' => $request->user_assigned_id,
                'deadline' => Carbon::parse($request->deadline . " " . $request->contact_time . ":00"),
                'status_id' => $request->status_id,
                'user_created_id' => auth()->id(),
                'external_id' => Uuid::uuid4()->toString(),
                'client_id' => $client->id
            ]
        );

        $lead->currency_id = $request->currency_id;
        $lead->lead_amount = $request->lead_amount;
        $lead->is_new = isset($request->is_new);
        $lead->business_line_id = $request->business_line_id;
        $lead->probability = $request->probability;
        $lead->amount_invoiced = $request->amount_invoiced;
        $lead->final_invoice_date = $request->final_invoice_date;
        $lead->next_steps = $request->next_steps;
        $lead->save();

        $insertedExternalId = $lead->external_id;
        Session()->flash('flash_message', __('Lead successfully added'));

        event(new \App\Events\LeadAction($lead, self::CREATED));
        Session()->flash('flash_message', __('Lead successfully added'));
        return redirect()->route('leads.show', $insertedExternalId);
    }

    public function updateProbability($lead, Request $request){
        $lead = Lead::where('external_id', $lead)->firstOrFail();
        $lead->probability = $request->probability;
        $lead->save();

        $result = [
            'probability' => $lead->probability,
            'face' => view('leads._face', ['lead' => $lead])->render()
        ];

        return $result;

    }

    /**
     * Update an existing resource in storage.
     *
     * @param StoreLeadRequest|Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(StoreLeadRequest $request, $external_id)
    {
        if ($request->client_external_id) {
            $client = Client::whereExternalId($request->client_external_id);
        }

        $lead = Lead::where('external_id', $external_id)->firstOrFail();
        $lead->title = $request->title;
        $lead->description = clean($request->description);
        $lead->user_assigned_id = $request->user_assigned_id;
        $lead->deadline = Carbon::parse($request->deadline . " " . $request->contact_time . ":00");
        $lead->status_id = $request->status_id;
        $lead->user_created_id = auth()->id();
        $lead->external_id = Uuid::uuid4()->toString();
        $lead->client_id = $client->id;

        $lead->currency_id = $request->currency_id;
        $lead->lead_amount = $request->lead_amount;
        $lead->is_new = isset($request->is_new);
        $lead->business_line_id = $request->business_line_id;
        $lead->probability = $request->probability;
        $lead->amount_invoiced = $request->amount_invoiced;
        $lead->final_invoice_date = $request->final_invoice_date;
        $lead->next_steps = $request->next_steps;
        $lead->save();

        $insertedExternalId = $lead->external_id;
        Session()->flash('flash_message', __('Lead successfully added'));

        event(new \App\Events\LeadAction($lead, self::CREATED));
        Session()->flash('flash_message', __('Lead successfully added'));
        return redirect()->route('leads.show', $insertedExternalId);
    }    

    public function updateAssign($external_id, Request $request)
    {
        $lead = $this->findByExternalId($external_id);
        $input = $request->get('user_assigned_id');
        $input = array_replace($request->all());
        $lead->fill($input)->save();
        $insertedName = $lead->user->name;

        event(new \App\Events\LeadAction($lead, self::UPDATED_ASSIGN));
        Session()->flash('flash_message', __('New user is assigned'));
        return redirect()->back();
    }

    /**
     * Update the follow up date (Deadline)
     * @param UpdateLeadFollowUpRequest $request
     * @param $external_id
     * @return mixed
     */
    public function updateFollowup(UpdateLeadFollowUpRequest $request, $external_id)
    {
        if (!auth()->user()->can('lead-update-deadline')) {
            session()->flash('flash_message_warning', __('You do not have permission to change task deadline'));
            return redirect()->route('tasks.show', $external_id);
        }
        $lead = $this->findByExternalId($external_id);
        $lead->fill(['deadline' => Carbon::parse($request->deadline . " " . $request->contact_time . ":00")])->save();
        event(new \App\Events\LeadAction($lead, self::UPDATED_DEADLINE));
        Session()->flash('flash_message', __('New follow up date is set'));
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int $external_id
     * @return \Illuminate\Http\Response
     */
    public function show($external_id)
    {
        $lead = $this->findByExternalId($external_id);
        return view('leads.show')
            ->with('filesystem_integration',Integration::whereApiType('file')->first())
            ->withLead($lead)
            ->withUsers(User::with(['department'])->get()->pluck('nameAndDepartmentEagerLoading', 'id'))
            ->withCompanyname(Setting::first()->company)
            ->withDocuments(Document::whereSourceType(Lead::class)->where('source_id', $lead->id)->get())
            ->withStatuses(Status::typeOfLead()->pluck('title', 'id'));
    }

    /**
     * Complete lead
     * @param $external_id
     * @param Request $request
     * @return mixed
     */
    public function updateStatus($external_id, Request $request)
    {
        if (!auth()->user()->can('lead-update-status')) {
            session()->flash('flash_message_warning', __('You do not have permission to change lead status'));
            return redirect()->route('tasks.show', $external_id);
        }
        $lead = $this->findByExternalId($external_id);
        if (isset($request->closeLead) && $request->closeLead === true) {
            $lead->status_id = Status::typeOfLead()->where('title', env('STATUS_CLOSED'))->first()->id;
            $lead->save();
        } else {
            $lead->fill($request->all())->save();
        }
        event(new \App\Events\LeadAction($lead, self::UPDATED_STATUS));
        Session()->flash('flash_message', __('Lead status updated'));
        return redirect()->back();
    }

    public function convertToQualifiedLead(Lead $lead)
    {
        Session()->flash('flash_message', __('Lead status updated'));
        return $lead->convertToQualified();
    }


    public function convertToOrder(Lead $lead)
    {
        $invoice = $lead->canConvertToOrder();
        return $invoice->external_id;
    }
    /**
     * @param $external_id
     * @return mixed
     */
    public function findByExternalId($external_id)
    {
        return Lead::whereExternalId($external_id)->first();
    }
    
    public function index(){
        return view('leads.index');
    }

    /**
     * Data for Data tables
     * @return mixed
     */
    public function allData()
    {
        $leads = Lead::all();

        $leads->map(function ($item) {
            return [$item['visible_deadline_date'] = $item['deadline']->format(carbonDate()), $item["visible_deadline_time"] = $item['deadline']->format(carbonTime())];
        });
        
        return DataTables::of($leads)
            ->addColumn('namelink', function ($lead) {
                return '<a href="/leads/'.$lead->external_id.'" ">'.$lead->title.'</a>';
            })
            ->addColumn('statustext', function($lead){
                return $lead->status->title;
            })
            ->rawColumns(['namelink'])
            ->make(true);
    }

/**
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function upload(Request $request, $external_id)
    {
        $lead = Lead::whereExternalId($external_id)->first();

        if (!is_null($request->files)) {
            foreach($request->file('files') as $image){
                $file = $image;
                $filename = str_random(8) . '_' . $file->getClientOriginalName();
                $fileOrginal = $file->getClientOriginalName();

                $size = $file->getSize();
                $mbsize = $size / 1048576;
                $totaltsize = substr($mbsize, 0, 4);

                if ($totaltsize > 15) {
                    Session::flash('flash_message', __('File Size cannot be bigger than 15MB'));
                    return redirect()->back();
                }

                $folder = "leads/" .  $external_id;
                $fileSystem = GetStorageProvider::getStorage();
                $fileData = $fileSystem->upload($folder, $filename, $file);

                Document::create([
                    'external_id' => Uuid::uuid4()->toString(),
                    'path' => $fileData['file_path'],
                    'size' => $totaltsize,
                    'original_filename' => $fileOrginal,
                    'source_id' => $lead->id,
                    'source_type' => Lead::class,
                    'mime' => $file->getClientMimeType(),
                    'integration_id' => isset($fileData['id']) ? $fileData['id'] : null,
                    'integration_type' => get_class($fileSystem)
                ]);
            }
            
        }
        Session::flash('flash_message', __('File successfully uploaded'));
        return $lead->external_id;
    }    

}