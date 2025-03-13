import { useParams } from "react-router-dom";
import { useState, useEffect } from "react";
import authConfig from "../auth/authConfig.json";
import styles from "../styles/songDetail.module.css";
import Wrapper from "../components/Wrapper";
import Navbar from "../components/Navbar";

const SongDetailPage = () => {
  const { songId } = useParams();
  const [songDetails, setSongDetails] = useState(null);
  const [error, setError] = useState(null);
  const [loading, setLoading] = useState(true);

  const clientId = authConfig.clientId;
  const clientSecret = authConfig.clientSecret;

  useEffect(() => {
    const fetchSongDetails = async () => {
      const authParameters = {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `grant_type=client_credentials&client_id=${clientId}&client_secret=${clientSecret}`,
      };

      try {
        const authResponse = await fetch('https://accounts.spotify.com/api/token', authParameters);
        const authData = await authResponse.json();
        const accessToken = authData.access_token;

        const songRes = await fetch(`https://api.spotify.com/v1/tracks/${songId}`, {
          headers: {
            Authorization: `Bearer ${accessToken}`,
          },
        });

        if (!songRes.ok) {
          throw new Error("Failed to fetch song details.");
        }

        const songData = await songRes.json();
        setSongDetails(songData);
      } catch (error) {
        setError("Error fetching song details.");
      } finally {
        setLoading(false);
      }
    };

    fetchSongDetails();
  }, [songId, clientId, clientSecret]);

  const searchOnYouTube = () => {
    if (!songDetails) return;

    const songTitle = songDetails.name;
    const artistNames = songDetails.artists.map(artist => artist.name).join(", ");
    
    const query = `${songTitle} ${artistNames}`;
    const youtubeSearchUrl = `https://www.youtube.com/results?search_query=${encodeURIComponent(query)}`;

    window.open(youtubeSearchUrl, "_blank");
  };

  const downloadSpotify = () => {
    if (!songDetails) return;

    const spotifyDownloadUrl = `https://spotidownloader.com/`;

    window.open(spotifyDownloadUrl, "_blank");
  }

  if (loading) {
    return <div>Loading...</div>;
  }

  if (error) {
    return <div>{error}</div>;
  }

  return (
    <Wrapper>
        <Navbar/>
        <div className={styles.songDetailPage}>
        <h2>{songDetails.name}</h2>
        <img src={songDetails.album.images[0]?.url} alt={songDetails.name} width="200" />
        <p>Artist: {songDetails.artists.map(artist => artist.name).join(", ")}</p>
        <p>Album: {songDetails.album.name}</p>
        <p>Release Date: {songDetails.album.release_date}</p>
        <p>Duration: {(songDetails.duration_ms / 1000).toFixed(0)} seconds</p>
          <div className="LookItUp">
          <a href={songDetails.external_urls.spotify} target="_blank" rel="noopener noreferrer">Open in Spotify</a>
          <button onClick={searchOnYouTube}>Find on YouTube</button>
          </div>
          <div className="DownloadIt">
          <button onClick={downloadSpotify}>Download Song</button>
          </div>
        </div>
    </Wrapper>
  );
};

export default SongDetailPage;
