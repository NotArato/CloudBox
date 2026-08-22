// Supabase Auth Helper Functions
const Auth = {
    async getCurrentUser() {
        if (!window.supabaseClient) return null;
        
        try {
            const { data: { session } } = await window.supabaseClient.auth.getSession();
            if (!session || !session.user) return null;
            
            // Try fetching profile from Supabase Database
            let profile = null;
            try {
                const { data } = await window.supabaseClient
                    .from('profiles')
                    .select('*')
                    .eq('id', session.user.id)
                    .single();
                profile = data;
            } catch (e) {
                console.warn('Profile fetch warning:', e);
            }

            return {
                ...session.user,
                profile: profile || {
                    name: session.user.user_metadata?.name || session.user.email.split('@')[0],
                    email: session.user.email,
                    is_premium: false,
                    storage_limit: 1073741824
                }
            };
        } catch (err) {
            console.error('getCurrentUser error:', err);
            return null;
        }
    },

    async requireAuth() {
        const user = await this.getCurrentUser();
        if (!user) {
            window.location.href = 'login.html';
        }
        return user;
    },

    async redirectIfAuthenticated() {
        const user = await this.getCurrentUser();
        if (user) {
            window.location.href = 'index.html';
        }
    },

    async signUp(name, email, password) {
        const { data, error } = await window.supabaseClient.auth.signUp({
            email,
            password,
            options: {
                data: { name }
            }
        });

        if (error) throw error;

        // Try creating profile record (non-blocking if RLS/table pending)
        if (data.user) {
            try {
                await window.supabaseClient.from('profiles').upsert({
                    id: data.user.id,
                    name: name,
                    email: email,
                    is_premium: false,
                    storage_limit: 1073741824
                });
            } catch (e) {
                console.warn('Profile upsert warning:', e);
            }
        }

        return data;
    },

    async signIn(email, password) {
        const { data, error } = await window.supabaseClient.auth.signInWithPassword({
            email,
            password
        });
        if (error) throw error;
        return data;
    },

    async signOut() {
        if (window.supabaseClient) {
            await window.supabaseClient.auth.signOut();
        }
        window.location.href = 'login.html';
    },

    async updateProfile(name) {
        const { data: { user } } = await window.supabaseClient.auth.getUser();
        if (!user) throw new Error('Not authenticated');

        const { error } = await window.supabaseClient
            .from('profiles')
            .upsert({ id: user.id, name, email: user.email });

        if (error) throw error;
    },

    async updatePassword(newPassword) {
        const { error } = await window.supabaseClient.auth.updateUser({
            password: newPassword
        });
        if (error) throw error;
    }
};
