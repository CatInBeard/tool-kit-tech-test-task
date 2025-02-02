import React, {useState} from 'react';

const Login = ({setPage, getToken, setToken}) => {

    const [formData, setFormData] = useState({
        email: '',
        password: '',
    });

    const [error, setError] = useState('');
    const [success, setSuccess] = useState('');

    const handleChange = (e) => {
        const { name, value, files } = e.target;
        setFormData({ ...formData, [name]: value });
    };

    const [isSubmitting, setIsSubmitting] = useState(false);


    const handleSubmit = async (e) => {
        e.preventDefault();
        setIsSubmitting(true);
        setSuccess('')


        const data = new FormData();
        data.append('email', formData.email);
        data.append('password', formData.password);

        try {
            const response = await fetch('/api/auth', {
                method: 'POST',
                body: data,
            });

            if (response.ok) {
                const result = await response.json();

                if (result.token) {
                    console.log('Success:', result);
                    setToken(result.token)
                    setPage('home')
                    return
                } else {
                    console.error('Token not found:', result);
                    setError('Invalid response');
                }
                setError('');
            } else if (response.status === 429) {
                setError('Too many requests. Please try again later.');
            } else {
                const error = await response.json();
                setError('Invalid email or password');
                console.error('Error:', error);
            }
        } catch (error) {
            console.error('Error:', error);
        } finally {
            setIsSubmitting(false);
        }
    };

    return (
        <div>
            <h1 className={"text-center"}>There is a login page</h1>
            <p>You can use seeded user admin@example.com with password 123</p>
            <br/>
            {error && <div className="alert alert-danger">{error}</div>}
            {success && <div className="alert alert-success">{success}</div>}
            <form onSubmit={handleSubmit} >
                <div className="form-group">
                    <label htmlFor="email">Email address</label>
                    <input type="email" className="form-control" id="email" aria-describedby="emailHelp" onChange={handleChange}
                           placeholder="Enter email" required={true} name="email" disabled={isSubmitting}/>
                </div>
                <div className="form-group mt-3">
                    <label htmlFor="password">Password</label>
                    <input type="password" className="form-control" id="password" placeholder="Password" onChange={handleChange}
                           required={true} name="password" disabled={isSubmitting}/>
                </div>

                <button type="submit" className="btn btn-primary mt-5">Submit</button>
            </form>
        </div>
    );
};

export default Login;
