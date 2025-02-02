import React, {useEffect, useState} from 'react';

const Home = ({setPage, getToken}) => {

    const [formData, setFormData] = useState({
        userId: '',
        email: '',
        name: '',
        password: '',
        passwordConfirm: '',
        tgName: '',
        phone: '',
    });

    let token = getToken();

    useEffect(() => {
        const fetchUserData = async () => {
            try {
                const response = await fetch('/api/users/me', {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                    },
                });

                if (response.ok) {
                    const data = await response.json();
                    setFormData({
                        email: data.email || '',
                        name: data.name || '',
                        role: data.role || '',
                        userId: data.id || '',
                        password: '',
                        passwordConfirm: '',
                        tgName: data.tgName || '',
                        phone: data.phone || '',
                    });
                } else {
                    const error = await response.json();
                    setError('Failed to fetch user data');
                    console.error('Error:', error);
                }
            } catch (error) {
                console.error('Error:', error);
                setError('An error occurred while fetching user data');
            }
        };

        fetchUserData();
    }, [token]);

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

        if (formData.password && formData.password !== formData.passwordConfirm) {
            setError('Password does not match');
            setIsSubmitting(false);
            return;
        }

        const data = new FormData();
        data.append('name', formData.name);
        data.append('email', formData.email);
        data.append('cat_photo', formData.catPhoto);
        if(formData.password){
            data.append('password', formData.password);
        }
        data.append('tg_name', formData.tgName);
        data.append('phone', formData.phone);

        try {
            const response = await fetch('/api/users/' + formData.userId , {
                method: 'PUT',
                headers: {
                    'Authorization': `Bearer ${token}`,
                },
                body: data,
            });

            if (response.ok) {
                const result = await response.text();
                console.log('Success:', result);
                setSuccess('Profile updated')
                setError('');
            } else if (response.status === 429) {
                setError('Too many requests. Please try again later.');
            } else {
                const error = await response.json();
                setError('Can\'t update profile, currently no good error handling, so you can get error in console');
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
            <h1 className={"text-center"}>Your profile edit page</h1>
            <br/>
            <p>Here you can see your profile</p>
            {error && <div className="alert alert-danger">{error}</div>}
            {success && <div className="alert alert-success">{success}</div>}
            <form onSubmit={handleSubmit} >
                <div className="form-group">
                    <label htmlFor="email">Email address</label>
                    <input type="email" className="form-control" id="email" aria-describedby="emailHelp" onChange={handleChange}
                           placeholder="Enter email" name="email" disabled={isSubmitting} value={formData.email}/>
                    <small id="emailHelp" className="form-text text-muted">We'll never share your email with anyone
                        else.</small>
                </div>
                <div className="form-group">
                    <label htmlFor="name">Name</label>
                    <input type="text" className="form-control" id="name" onChange={handleChange}
                           placeholder="Enter name" name="name" disabled={isSubmitting} value={formData.name}/>
                </div>
                <div className="form-group">
                    <label htmlFor="password">Password</label>
                    <input type="password" className="form-control" id="password" placeholder="Password" onChange={handleChange}
                           name="password" disabled={isSubmitting}/>
                </div>
                <div className="form-group">
                    <label htmlFor="passwordConfirm">Password confirm</label>
                    <input type="password" className="form-control" id="passwordConfirm" placeholder="Password" onChange={handleChange}
                           name="passwordConfirm" disabled={isSubmitting}/>
                </div>
                <div className="form-group">
                    <label htmlFor="tgName">Telegram username</label>
                    <input type="text" className="form-control" id="tgName" placeholder="@tgname" onChange={handleChange}
                           name="tg_name" disabled={isSubmitting} value={formData.tgName} />
                </div>
                <div className="form-group">
                    <label htmlFor="phone">Phone number</label>
                    <input type="tel" className="form-control" id="phone" placeholder="9998887766" onChange={handleChange}
                           name="phone" disabled={isSubmitting} value={formData.phone} />
                </div>


                <div className="mb-3">
                    <label htmlFor="catPhoto" className="form-label">Default file input example</label>
                    <input className="form-control" type="file" id="catPhoto" accept={"image/*"} onChange={handleChange}
                           name="catPhoto" disabled={isSubmitting} />
                    <small className="form-text text-muted">The upload of a cat photo is not necessary, but it will delight
                        the person reviewing the questionary</small>
                </div>



                <button type="submit" className="btn btn-primary">Submit</button>
            </form>
        </div>
    );
};

export default Home;
