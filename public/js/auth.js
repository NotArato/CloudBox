// Supabase Auth Helper Functions
const Auth = {
    async getCurrentUser() {
        const { data: { session } } = await window.supabaseClient.auth.getSession();
        if (!session) return null;
        
        // Fetch or auto-create profile
        let { data: profile } = await window.supabaseClient
            .from('profiles')
            .select('*')
            .eq('id', session.user.id)
            .single();

        if (!profile) {
            const { data: newProfile } = await window.supabaseClient
                .from('profiles')
                .insert({
                    id: session.user.id,
                    name: session.user.user_metadata?.name || session.user.email.split('@')[0],
                    email: session.user.email,
                    is_premium: false,
                    storage_limit: 1073741824 // 1GB
                })
                .select()
                .single();
            profile = newProfile;
        }

        return {
            ...session.user,
            profile: profile || {
                name: session.user.email.split('@')[0],
                email: session.user.email,
                is_premium: false,
                storage_limit: 1073741824
            }
        };
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

        if (data.user) {
            await window.supabaseClient.from('profiles').upsert({
                id: data.user.id,
                name: name,
                email: email,
                is_premium: false,
                storage_limit: 1073741824
            });
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
        await window.supabaseClient.auth.signOut();
        window.location.href = 'login.html';
    },

    async updateProfile(name) {
        const { data: { user } } = await window.supabaseClient.auth.getUser();
        if (!user) throw new Error('Not authenticated');

        const { error } = await window.supabaseClient
            .from('profiles')
            .update({ name })
            .eq('id', user.id);

        if (error) throw error;
    },

    async updatePassword(newPassword) {
        const { error } = await window.supabaseClient.auth.updateUser({
            password: newPassword
        });
        if (error) throw error;
    },

    async upgradeToPremium() {
        const { data: { user } } = await window.supabaseClient.auth.getUser();
        if (!user) throw new Error('Not authenticated');

        const { error } = await window.supabaseClient
            .from('profiles')
            .update({ 
                is_premium: true,
                storage_limit: 5368709120 // 5GB
            })
            .eq('id', user.id);

        if (error) throw error;
    }
};
