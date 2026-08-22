// CloudBox Client-Side Configuration
window.CLOUDBOX_CONFIG = {
    SUPABASE_URL: "https://yocsxzpjlogexfjdwziu.supabase.co",
    SUPABASE_ANON_KEY: "sb_publishable_jIAUfDPQFdGcHhYsdo4mPQ_iyX7C_UR",
    R2_CONFIG: {
        endpoint: "https://9dd2b1dbab21dc90c9f2dcd96ae1c5c9.r2.cloudflarestorage.com",
        accessKeyId: "481828fa90e5ee5a46dfd3393b8604bd",
        secretAccessKey: "595a9c9ab4773c638ee553d36f4e9cbd1a1054e826c5e67ce64bd995f4cf9b64",
        bucket: "cloudbox",
        region: "auto"
    }
};

// Initialize Supabase Client Instance
if (window.supabase) {
    window.supabaseClient = window.supabase.createClient(
        window.CLOUDBOX_CONFIG.SUPABASE_URL,
        window.CLOUDBOX_CONFIG.SUPABASE_ANON_KEY
    );
}
