import Wrapper from "../components/Wrapper";
import { useState, useEffect } from "react";
import styles from "../styles/homepage.module.css";
import Navbar from "../components/Navbar";
import authConfig from "../auth/authConfig.json";
import Footer from "../components/Footer";
import { useNavigate } from "react-router-dom"; // For navigation
import Library from "../components/LibrarySide";

const HomePage = () => {
  const [searchQuery, setSearchQuery] = useState("");
  const [accessToken, setAccessToken] = useState("");
  const [songs, setSongs] = useState([]);
  const [error, setError] = useState(null);
  const [loading, setLoading] = useState(false);
  const navigate = useNavigate();

  const clientId = authConfig.clientId;
  const clientSecret = authConfig.clientSecret;
  const redirectUri = authConfig.redirectUri;

  useEffect(() => {
    const authParameters = {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: `grant_type=client_credentials&client_id=${clientId}&client_secret=${clientSecret}`
    };

    fetch('https://accounts.spotify.com/api/token', authParameters)
      .then(result => result.json())
      .then(data => setAccessToken(data.access_token));
  }, []);

  const navigateToSongDetail = (trackId) => {
    navigate(`/song/${trackId}`);
  };

  return (
    <Wrapper>
      <header className={styles.header}>
        <Navbar />
      </header>
      <div className={styles.pageLayout}>
          <Library />
        <section className={styles.featured}>
          <h2 className={styles.sectionTitle}>Search Results</h2>
          {loading ? (
            <div className={styles.loading}>Loading...</div>
          ) : error ? (
            <div className={styles.error}>{error}</div>
          ) : (
            <div className={styles.grid}>
              {songs.map((song, index) => (
                <div key={index} className={styles.card} onClick={() => navigateToSongDetail(song.id)}>
                  <img src={song.album.images[0]?.url} alt={song.name} width="100" />
                  <h3 className={styles.cardTitle}>{song.name}</h3>
                  <p>{song.artists[0]?.name}</p>
                </div>
              ))}
            </div>
          )}
        </section>
      </div>

      <Footer />
    </Wrapper>
  );
};

export default HomePage;
