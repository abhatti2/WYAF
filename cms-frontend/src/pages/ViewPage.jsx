import React, { useState, useEffect } from 'react';
import { useParams } from 'react-router-dom';

const ViewPage = () => {
    const { id } = useParams();
    const [page, setPage] = useState(null);

    useEffect(() => {
        fetch(`http://127.0.0.1:5173/view_page.php?id=${id}`)
            .then(response => response.json())
            .then(data => setPage(data));
    }, [id]);

    return (
        <div className="view-page">
            {page ? (
                <>
                    <h1>{page.title}</h1>
                    <div dangerouslySetInnerHTML={{ __html: page.content }} />
                    {page.image && <img src={`http://127.0.0.1:5173/${page.image}`} alt={page.title} />}
                </>
            ) : (
                <p>Loading...</p>
            )}
        </div>
    );
};

export default ViewPage;
