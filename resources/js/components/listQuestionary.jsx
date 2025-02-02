import React, { useEffect, useState } from 'react';

const ListQuestionary = ({ setPage, getToken }) => {

    const [data, setData] = useState([]);
    const [currentPage, setCurrentPage] = useState(1);
    const [totalPages, setTotalPages] = useState(0);
    const [error, setError] = useState('');
    const [success, setSuccess] = useState('');

    let token = getToken();

    useEffect(() => {
        const fetchData = async () => {
            try {
                const response = await fetch(`/api/questionary?page=${currentPage}&limit=15`, {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                    },
                });

                if (response.ok) {
                    const result = await response.json();
                    setData(result.data || []);
                    setTotalPages(result.totalPages || 0);
                } else {
                    const error = await response.json();
                    setError('Failed to fetch data');
                    console.error('Error:', error);
                }
            } catch (error) {
                console.error('Error:', error);
                setError('An error occurred while fetching data');
            }
        };

        fetchData();
    }, [currentPage, token]);

    const handlePageChange = (page) => {
        setCurrentPage(page);
    };

    return (
        <div>
            {error && <div className="error">{error}</div>}
            {success && <div className="success">{success}</div>}
            <ul>
                {data.map(item => (
                    <div className={"card m-2 p-2"} key={item.id}>
                        <p>Email: {item.email}</p>
                        <p>Name: {item.name}</p>
                        <p>Telegram Name: {item.tg_name}</p>
                        <p>Phone: {item.phone}</p>
                        {item.cat_photo && <img src={item.cat_photo} alt={"cat photo"} style={{
                            maxWidth: '50vw',
                            maxHeight: '70vh',
                            objectFit: 'contain',
                        }}/>}
                    </div>
                ))}
            </ul>
            <div className="pagination">
                {Array.from({ length: totalPages }, (_, index) => (
                    <button
                        key={index + 1}
                        onClick={() => handlePageChange(index + 1)}
                        disabled={currentPage === index + 1}
                    >
                        {index + 1}
                    </button>
                ))}
            </div>
        </div>
    );
};

export default ListQuestionary;
