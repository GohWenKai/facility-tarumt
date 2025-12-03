<?php

namespace App\Factories;

use App\Models\Asset;

class AssetFactory
{
    /**
     * Handle logic before CREATING an asset
     */
    public static function create(array $data): Asset
    {
        // 1. Data Sanitization (Name & Type)
        // Ensure name is "Title Case" (e.g., "sony projector" -> "Sony Projector")
        if (isset($data['name'])) {
            $data['name'] = trim(ucwords(strtolower($data['name'])));
        }

        // Ensure type is "Sentence case" (e.g., "ELECTRONICS" -> "Electronics")
        if (isset($data['type'])) {
            $data['type'] = trim(ucfirst(strtolower($data['type'])));
        }

        // 2. Business Logic: Maintenance Note
        // If condition is Good/Fair, force maintenance_note to be NULL
        if (isset($data['condition']) && in_array($data['condition'], ['Good', 'Fair'])) {
            $data['maintenance_note'] = null;
        }

        return Asset::create($data);
    }

    /**
     * Handle logic before UPDATING an asset
     */
    public static function update(Asset $asset, array $data): bool
    {
        // 1. Data Sanitization (Name & Type)
        if (isset($data['name'])) {
            $data['name'] = trim(ucwords(strtolower($data['name'])));
        }

        if (isset($data['type'])) {
            $data['type'] = trim(ucfirst(strtolower($data['type'])));
        }

        // 2. Business Logic: Maintenance Note
        if (isset($data['condition']) && in_array($data['condition'], ['Good', 'Fair'])) {
            $data['maintenance_note'] = null;
        }
        
        return $asset->update($data);
    }
}