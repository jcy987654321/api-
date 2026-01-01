<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait HasAvatar
{
    /**
     * Update the user's avatar.
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @return string
     */
    public function updateAvatar(UploadedFile $file)
    {
        $this->deleteAvatar();

        $path = $file->store('avatars', 'public');
        
        $this->update([
            'avatar' => $path,
        ]);

        return $this->getAvatarUrl();
    }

    /**
     * Delete the user's avatar.
     *
     * @return void
     */
    public function deleteAvatar()
    {
        if ($this->avatar) {
            Storage::disk('public')->delete($this->avatar);
            $this->update(['avatar' => null]);
        }
    }

    /**
     * Get the URL to the user's avatar.
     *
     * @return string
     */
    public function getAvatarUrl()
    {
        if ($this->avatar) {
            return Storage::disk('public')->url($this->avatar);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }
}
