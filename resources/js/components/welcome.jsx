import React, {useState} from 'react';

const Welcome = ({setPage, getToken}) => {

    const [formData, setFormData] = useState({
        email: '',
        name: '',
        password: '',
        passwordConfirm: '',
        tgName: '',
        phone: '',
        catPhoto: null,
    });

    let token = getToken();

    const [error, setError] = useState('');
    const [success, setSuccess] = useState('');

    const handleChange = (e) => {
        const { name, value, files } = e.target;
        if (name === 'catPhoto') {
            setFormData({ ...formData, [name]: files[0] });
        } else {
            setFormData({ ...formData, [name]: value });
        }
    };

    const [isSubmitting, setIsSubmitting] = useState(false);

    const handleLogin = async => {
        setPage('login')
    }

    const handleGoHome = async => {
        setPage('home')
    }

    const handleSubmit = async (e) => {
        e.preventDefault();
        setIsSubmitting(true);
        setSuccess('')

        if (formData.password !== formData.passwordConfirm) {
            setError('Password does not match');
            setIsSubmitting(false);
            return;
        }

        const data = new FormData();
        data.append('name', formData.name);
        data.append('email', formData.email);
        data.append('password', formData.password);
        data.append('tg_name', formData.tgName);
        data.append('phone', formData.phone);
        data.append('cat_photo', formData.catPhoto);

        try {
            const response = await fetch('/api/questionary', {
                method: 'POST',
                body: data,
            });

            if (response.ok) {
                const result = await response.text();
                console.log('Success:', result);
                setSuccess(<p>Your questionary submitted, please wait email with confirmation <a href={"http://localhost:8025"}>go to mailpit</a></p>)
                setError('');
            } else if (response.status === 429) {
                setError('Too many requests. Please try again later.');
            } else {
                const error = await response.json();
                setError('Can\'t create questionary, currently no good error handling, so you can get error in console');
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
            <div>
                {!token && <div className={"btn btn-primary float-end"} onClick={handleLogin}>Login</div>}
                {token && <div className={"btn btn-success float-end"} onClick={handleGoHome}>You are logged in, go home</div>}
            </div>
            <h1 className={"text-center"}>Welcome to test task!</h1>
            <p>You can open openapi doc: <a href={"/docs"}>here</a></p>
            <br/>
            <p>Here you can apply small questionary to create an account:</p>
            {error && <div className="alert alert-danger">{error}</div>}
            {success && <div className="alert alert-success">{success}</div>}
            <form onSubmit={handleSubmit} >
                <div className="form-group">
                    <label htmlFor="email">Email address</label>
                    <input type="email" className="form-control" id="email" aria-describedby="emailHelp" onChange={handleChange}
                           placeholder="Enter email" required={true} name="email" disabled={isSubmitting}/>
                    <small id="emailHelp" className="form-text text-muted">We'll never share your email with anyone
                        else.</small>
                </div>
                <div className="form-group">
                    <label htmlFor="name">Name</label>
                    <input type="text" className="form-control" id="name" onChange={handleChange}
                           placeholder="Enter name" required={true} name="name" disabled={isSubmitting}/>
                </div>
                <div className="form-group">
                    <label htmlFor="password">Password</label>
                    <input type="password" className="form-control" id="password" placeholder="Password" onChange={handleChange}
                           required={true} name="password" disabled={isSubmitting}/>
                </div>
                <div className="form-group">
                    <label htmlFor="passwordConfirm">Password confirm</label>
                    <input type="password" className="form-control" id="passwordConfirm" placeholder="Password" onChange={handleChange}
                           required={true} name="passwordConfirm" disabled={isSubmitting}/>
                </div>
                <div className="form-group">
                    <label htmlFor="tgName">Telegram username</label>
                    <input type="text" className="form-control" id="tgName" placeholder="@tgname" onChange={handleChange}
                           name="tg_name" disabled={isSubmitting} />
                </div>
                <div className="form-group">
                    <label htmlFor="phone">Phone number</label>
                    <input type="tel" className="form-control" id="phone" placeholder="9998887766" onChange={handleChange}
                           name="phone" disabled={isSubmitting} />
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

export default Welcome;
