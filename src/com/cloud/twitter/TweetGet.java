package com.cloud.twitter;

import java.io.BufferedReader;
import java.io.FileReader;
import java.io.IOException;

import twitter4j.StallWarning;
import twitter4j.Status;
import twitter4j.StatusDeletionNotice;
import twitter4j.StatusListener;
import twitter4j.TwitterException;
import twitter4j.TwitterStream;
import twitter4j.TwitterStreamFactory;
import twitter4j.conf.ConfigurationBuilder;

/**
 * <p>This is a code example of Twitter4J Streaming API - sample method support.<br>
 * Usage: java twitter4j.examples.PrintSampleStream<br>
 * </p>
 *
 * @author Yusuke Yamamoto - yusuke at mac.com
 */
public final class TweetGet {
    /**
     * Main entry of this application.
     *
     * @param args
     * @throws IOException 
     */
    public static void main(String[] args) throws TwitterException, IOException {
        BufferedReader br = null;
        String cKey = null;
        String cSecret = null;
        String aToken = null;
        String aTokenSecret = null;
        
        MyDynamoDB tweetbase = new MyDynamoDB("Twitable");
        
        try{
            br = new BufferedReader(new FileReader(args[0]));
            cKey = br.readLine();
            cSecret = br.readLine();
            aToken = br.readLine();
            aTokenSecret = br.readLine();
        }
        catch(ArrayIndexOutOfBoundsException e){
            System.err.println("usage: java TweetGet.java <OAuth.credentials>");
            System.exit(1);
        }
        catch(IOException e){
            System.err.println("error: w/ "+ args[0]+", check file and try again");
            System.exit(1);
        }
        finally{
            if(br != null)
                br.close();
        }
    	//just fill this
    	 ConfigurationBuilder cb = new ConfigurationBuilder();
         cb.setDebugEnabled(true)
           .setOAuthConsumerKey(cKey)
           .setOAuthConsumerSecret(cSecret)
           .setOAuthAccessToken(aToken)
           .setOAuthAccessTokenSecret(aTokenSecret);
         
        TwitterStream twitterStream = new TwitterStreamFactory(cb.build()).getInstance();
        StatusListener listener = new StatusListener() {
            @Override
            public void onStatus(Status status) {
            	if(status.getGeoLocation() != null){
            		tweetbase.addTweet(status.getId(),
            			status.getUser().getScreenName(), 
            			status.getText(), 
            			status.getFavoriteCount(), 
            			status.getRetweetCount(), 
            			status.getGeoLocation().getLongitude(),
            			status.getGeoLocation().getLatitude());
            	}
            }

            @Override
            public void onDeletionNotice(StatusDeletionNotice statusDeletionNotice) {
                //System.out.println("Got a status deletion notice id:" + statusDeletionNotice.getStatusId());
            }

            @Override
            public void onTrackLimitationNotice(int numberOfLimitedStatuses) {
                //System.out.println("Got track limitation notice:" + numberOfLimitedStatuses);
            }

            @Override
            public void onScrubGeo(long userId, long upToStatusId) {
                //System.out.println("Got scrub_geo event userId:" + userId + " upToStatusId:" + upToStatusId);
            }

            @Override
            public void onStallWarning(StallWarning warning) {
                //System.out.println("Got stall warning:" + warning);
            }

            @Override
            public void onException(Exception ex) {
                ex.printStackTrace();
            }
        };
        twitterStream.addListener(listener);
        twitterStream.sample();
    }
}
