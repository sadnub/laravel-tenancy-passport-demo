//Dashboard Components
import DashboardLayout from '@/components/Layouts/DashboardLayout.vue'
import Dashboard from '@/components/Views/Dashboard.vue'
import Tickets from '@/components/Views/Tickets.vue'

//Auth Components
import AuthLayout from '@/components/Layouts/AuthLayout.vue'
import Register from '@/components/Auth/Register.vue'
import Login from '@/components/Auth/Login.vue'
import ResetEmail from '@/components/Auth/ResetEmail.vue'
import ResetPassword from '@/components/Auth/ResetPassword.vue'

//Landing Components
import Welcome from '@/components/Landing/Welcome.vue'

//General Components
import NotFound from '@/components/General/NotFound.vue'

//Export routes based on domain used
const host = window.location.host.toUpperCase()

const routes = () => {

	//Test for portal routes
	if (host.includes('APP.ITPLOG.COM')) {

		return [
		  {path: '/', component: DashboardLayout,
		    children: [
		    	{path: 'dashboard', name: 'dashboard', component: Dashboard},
		    	{path: 'tickets', name: 'dashbaord.tickets', component: Tickets},
		    ]
			},
			{path: '/auth', component: AuthLayout,
				children: [
					{path: '/login', name: 'auth.login' ,component: Login},
			    {path: '/password/email', name: 'auth.email', component: ResetEmail},
			    {path: '/password/reset/:token', component: ResetPassword, props: true},
				]
			},
			{path: '*', component: NotFound}
		]

	//Fallback to landing page routes
	} else {

		return [
	    	{path: '/', name: 'landing.welcome', component: Welcome},
	    	{path: '/auth', component: AuthLayout,
	    		children: [
	    			{path: '/register', name: 'landing.register', component: Register}
	    		]
			},
			{path: '*', component: NotFound}
		]
	}
}

export default routes()