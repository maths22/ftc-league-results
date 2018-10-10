CREATE TABLE preferences (id VARCHAR PRIMARY KEY, value VARCHAR);
CREATE TABLE teams (number INTEGER PRIMARY KEY, advanced BOOLEAN, division INTEGER);
CREATE TABLE teamInfo (number INTEGER PRIMARY KEY, name VARCHAR, school VARCHAR, city VARCHAR, state VARCHAR, country VARCHAR, rookie INTEGER);
CREATE TABLE formRows (formID VARCHAR, row INTEGER, type INTEGER, columnCount INTEGER, description VARCHAR, rule VARCHAR, page INTEGER, PRIMARY KEY(formID, row));
CREATE TABLE formItems (formID VARCHAR, row INTEGER, itemIndex INTEGER, label VARCHAR, type INTEGER, PRIMARY KEY (formID, itemIndex), FOREIGN KEY (formID, row) REFERENCES formRows(formID,row));
CREATE TABLE formStatus (team INTEGER REFERENCES teams(number), formID VARCHAR, itemIndex INTEGER, value BOOLEAN, PRIMARY KEY (team, formID, itemIndex), FOREIGN KEY (formID, itemIndex) REFERENCES formItems(formID, itemIndex));
CREATE TABLE formComments (team INTEGER REFERENCES teams(number), formID VARCHAR, comment VARCHAR, PRIMARY KEY (team, formID));
CREATE TABLE formSigs (team INTEGER REFERENCES teams(number), formID VARCHAR, sigIndex INTEGER, sig VARCHAR, PRIMARY KEY (team, formID, sigIndex));
CREATE TABLE status (team INTEGER REFERENCES teams(number), stage VARCHAR, status INTEGER, PRIMARY KEY (team, stage));
CREATE TABLE station (type VARCHAR PRIMARY KEY, count INTEGER);
CREATE TABLE slots (start DATETIME, end DATETIME, type VARCHAR REFERENCES station(type), stationIndex INTEGER, team INTEGER, PRIMARY KEY(type, stationIndex, start));
CREATE TABLE matchSchedule (start DATETIME PRIMARY KEY, end DATETIME, type INTEGER, label VARCHAR);
CREATE TABLE blocks (start INTEGER PRIMARY KEY, type INTEGER, duration INTEGER, count INTEGER, label VARCHAR);
CREATE TABLE selections (id INTEGER PRIMARY KEY, op INTEGER, alliance INTEGER, team INTEGER REFERENCES teams(number));
CREATE TABLE alliances (rank INTEGER PRIMARY KEY, team1 INTEGER REFERENCES teams(number), team2 INTEGER REFERENCES teams(number), team3 INTEGER REFERENCES teams(number));
CREATE TABLE quals (match INTEGER PRIMARY KEY, red1 INTEGER REFERENCES teams(number), red1S BOOLEAN, red2 INTEGER REFERENCES teams(number), red2S BOOLEAN, blue1 INTEGER REFERENCES teams(number), blue1S BOOLEAN,blue2 INTEGER REFERENCES teams(number), blue2S BOOLEAN);
CREATE TABLE qualsData (match PRIMARY KEY REFERENCES quals(match), status INTEGER, randomization INTEGER, start DATETIME, scheduleStart DATETIME);
CREATE TABLE qualsResults (match INTEGER PRIMARY KEY REFERENCES quals(match), redScore INTEGER, blueScore INTEGER, redPenaltyCommitted INTEGER, bluePenaltyCommitted INTEGER);
CREATE TABLE qualsScores (match INTEGER REFERENCES quals(match), alliance INTEGER, card1 INTEGER, card2 INTEGER, dq1 INTEGER, dq2 INTEGER, noshow1 INTEGER, noshow2 INTEGER, major INTEGER, minor INTEGER, adjust INTEGER, PRIMARY KEY (match, alliance));
CREATE TABLE qualsGameSpecific (match INTEGER REFERENCES quals(match), alliance INTEGER, init1 INTEGER, init2 INTEGER, landed1 INTEGER, landed2 INTEGER, claimed1 INTEGER, claimed2 INTEGER, autoParking1 INTEGER, autoParking2 INTEGER, sampleFieldState INTEGER, depot INTEGER, gold INTEGER, silver INTEGER, latched1 INTEGER, latched2 INTEGER, endParked1 INTEGER, endParked2 INTEGER, PRIMARY KEY (match, alliance));
CREATE TABLE elims (match INTEGER PRIMARY KEY, red INTEGER REFERENCES alliances(rank), blue INTEGER REFERENCES alliances(rank));
CREATE TABLE elimsData (match PRIMARY KEY REFERENCES elims(match), status INTEGER, randomization INTEGER, start DATETIME);
CREATE TABLE elimsResults (match INTEGER PRIMARY KEY REFERENCES elims(match), redScore INTEGER, blueScore INTEGER, redPenaltyCommitted INTEGER, bluePenaltyCommitted INTEGER);
CREATE TABLE elimsScores (match INTEGER REFERENCES elims(match), alliance INTEGER, card INTEGER, dq INTEGER, noshow1 INTEGER, noshow2 INTEGER, noshow3 INTEGER, major INTEGER, minor INTEGER, adjust INTEGER, PRIMARY KEY (match, alliance));
CREATE TABLE elimsGameSpecific (match INTEGER REFERENCES elims(match), alliance INTEGER, init1 INTEGER, init2 INTEGER, landed1 INTEGER, landed2 INTEGER, claimed1 INTEGER, claimed2 INTEGER, autoParking1 INTEGER, autoParking2 INTEGER, sampleFieldState INTEGER, depot INTEGER, gold INTEGER, silver INTEGER, latched1 INTEGER, latched2 INTEGER, endParked1 INTEGER, endParked2 INTEGER, PRIMARY KEY (match, alliance));
CREATE TABLE awardInfo(id INTEGER PRIMARY KEY, name VARCHAR, description VARCHAR, teamAward BOOLEAN, editable BOOLEAN, required BOOLEAN, awardOrder INTEGER);
CREATE TABLE awardAssignment(id INTEGER PRIMARY KEY, winnerName VARCHAR, winnerTeam INTEGER REFERENCES teams(number), winnerDescription VARCHAR, secondName VARCHAR, secondTeam INTEGER REFERENCES teams(number), thirdName VARCHAR, thirdTeam INTEGER REFERENCES teams(number) );
CREATE TABLE sponsors (id INTEGER PRIMARY KEY, name VARCHAR, level INTEGER, logoPath VARCHAR);
CREATE TABLE config (key VARCHAR PRIMARY KEY, value VARCHAR);
CREATE TABLE leagueHistory (team INTEGER, eventCode VARCHAR, match INTEGER, rp INTEGER, tbp INTEGER, score INTEGER, PRIMARY KEY(team, eventCode, match));
CREATE TABLE leagueMeets (eventCode VARCHAR PRIMARY KEY, name VARCHAR, start DATETIME, end DATETIME);
CREATE TABLE leagueMembers (code VARCHAR, team INTEGER, PRIMARY KEY(code, team));
CREATE TABLE leagueInfo (code VARCHAR PRIMARY KEY, name VARCHAR, country VARCHAR, state VARCHAR, city VARCHAR);
CREATE TABLE leagueConfig (league VARCHAR, key VARCHAR, value VARCHAR, PRIMARY KEY (league, key));