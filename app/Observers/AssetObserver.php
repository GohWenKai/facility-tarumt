<?php

namespace App\Observers;

use App\Models\Asset;
use SimpleXMLElement;
use Illuminate\Support\Facades\Storage;

class AssetObserver
{
    private $ns = 'http://schemas.xmlsoap.org/soap/envelope/';

    /**
     * Handle the Asset "created" event.
     */
    public function created(Asset $asset)
    {
        // Audit Log - Track creation
        $auditLog = \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'created',
            'model_type' => Asset::class,
            'model_id' => $asset->id,
            'new_values' => $asset->toArray(),
            'ip_address' => request()->ip(),
        ]);

        // Check for suspicious activity
        $controller = app(\App\Http\Controllers\Admin\AuditLogController::class);
        $controller->checkSuspiciousActivity($auditLog);
        
        $soapEnvelope = new SimpleXMLElement('<soap:Envelope xmlns:soap="'.$this->ns.'"></soap:Envelope>');
        $soapBody = $soapEnvelope->addChild('Body', null, $this->ns);
        $assetNode = $soapBody->addChild('asset');

        // Add Data
        $assetNode->addChild('id', $asset->id);
        $assetNode->addChild('name', $asset->name);
        $assetNode->addChild('type', $asset->type);
        $assetNode->addChild('serial_number', $asset->serial_number);
        $assetNode->addChild('condition', $asset->condition);
        
        if ($asset->maintenance_note) {
            $assetNode->addChild('maintenance_note', $asset->maintenance_note);
        }
        
        // Timestamps
        $assetNode->addChild('created_at', $asset->created_at->toDateTimeString());

        Storage::put("/assets/{$asset->id}.xml", $soapEnvelope->asXML());
    }

    /**
     * Handle the Asset "updated" event.
     */
    public function updated(Asset $asset)
    {
        // Audit Log - Track updates
        $auditLog = \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'updated',
            'model_type' => Asset::class,
            'model_id' => $asset->id,
            'old_values' => $asset->getOriginal(),
            'new_values' => $asset->getDirty(),
            'ip_address' => request()->ip(),
        ]);

        // Check for suspicious activity
        $controller = app(\App\Http\Controllers\Admin\AuditLogController::class);
        $controller->checkSuspiciousActivity($auditLog);
        
        $path = "/assets/{$asset->id}.xml";
        
        // 1. Load existing XML or create new if missing
        if (Storage::exists($path)) {
            $xmlContent = Storage::get($path);
            $soapEnvelope = new SimpleXMLElement($xmlContent);
            // We need to access the body to append to it
            $soapBody = $soapEnvelope->children($this->ns)->Body;
        } else {
            $soapEnvelope = new SimpleXMLElement('<soap:Envelope xmlns:soap="'.$this->ns.'"></soap:Envelope>');
            $soapBody = $soapEnvelope->addChild('Body', null, $this->ns);
        }

        // 2. Create the Update Node
        $updateNode = $soapBody->addChild('asset_update');

        $updateNode->addChild('action', 'UPDATE');
        
        // =========================================================
        // FIX: Add Name and Type here so they appear in the log
        // =========================================================
        $updateNode->addChild('name', $asset->name);
        $updateNode->addChild('type', $asset->type);
        
        $updateNode->addChild('condition', $asset->condition);
        $updateNode->addChild('serial_number', $asset->serial_number);

        if ($asset->maintenance_note) {
            $updateNode->addChild('maintenance_note', $asset->maintenance_note);
        }
        
        $updateNode->addChild('logged_at', now()->toDateTimeString());

        // 3. Save
        Storage::put($path, $soapEnvelope->asXML());
    }

    /**
     * Handle the Asset "deleted" event.
     */
    public function deleted(Asset $asset)
    {
        // Audit Log - Track deletion
        $auditLog = \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'deleted',
            'model_type' => Asset::class,
            'model_id' => $asset->id,
            'old_values' => $asset->toArray(),
            'ip_address' => request()->ip(),
        ]);

        // Check for suspicious activity
        $controller = app(\App\Http\Controllers\Admin\AuditLogController::class);
        $controller->checkSuspiciousActivity($auditLog);
        
        $path = "assets/{$asset->id}.xml";
        if (Storage::exists($path)) {
            Storage::delete($path);
        }
    }
}