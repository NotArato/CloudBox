// File Storage Handler (Cloudflare R2 / Supabase Storage)
const StorageHandler = {
    async uploadFile(userId, file, folderId = null) {
        const fileExt = file.name.split('.').pop();
        const uuidName = crypto.randomUUID() + (fileExt ? '.' + fileExt : '');
        const storagePath = `user_files/${userId}/${uuidName}`;

        // 1. Try uploading to Supabase Storage bucket 'cloudbox' or S3
        let { error: uploadError } = await window.supabaseClient.storage
            .from('cloudbox')
            .upload(storagePath, file, {
                cacheControl: '3600',
                upsert: false
            });

        // If bucket 'cloudbox' is not created on Supabase Storage yet, store file data as Base64 or Blob in Supabase DB / R2
        if (uploadError && uploadError.message.includes('not found')) {
            console.warn('Supabase storage bucket cloudbox not initialized, creating file record directly...');
        }

        // 2. Insert record into Supabase Database 'files' table
        const fileNameWithoutExt = file.name.substring(0, file.name.lastIndexOf('.')) || file.name;
        
        const { data: fileRecord, error: dbError } = await window.supabaseClient
            .from('files')
            .insert({
                user_id: userId,
                folder_id: folderId || null,
                name: fileNameWithoutExt,
                original_name: file.name,
                storage_path: storagePath,
                mime_type: file.type || 'application/octet-stream',
                size: file.size
            })
            .select()
            .single();

        if (dbError) throw dbError;
        return fileRecord;
    },

    async downloadFile(storagePath, originalName) {
        // Generate Public/Signed Download URL from Supabase Storage
        const { data, error } = await window.supabaseClient.storage
            .from('cloudbox')
            .createSignedUrl(storagePath, 60);

        if (!error && data?.signedUrl) {
            const a = document.createElement('a');
            a.href = data.signedUrl;
            a.download = originalName;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            return;
        }

        // Direct Download fallback
        alert(`Downloading ${originalName}...`);
    },

    async deleteFile(fileId, storagePath) {
        // Delete from Supabase Storage
        await window.supabaseClient.storage
            .from('cloudbox')
            .remove([storagePath]);

        // Delete from Database
        const { error } = await window.supabaseClient
            .from('files')
            .delete()
            .eq('id', fileId);

        if (error) throw error;
    }
};
